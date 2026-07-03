<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Strategy Pattern: mỗi cách sắp xếp sản phẩm là một Strategy có
 * thể hoán đổi được, inject vào ProductService để áp dụng.
 */
interface ProductSortStrategy
{
    public function apply(Builder $query): Builder;
    public function code(): string;
    public function label(): string;
}

class NewestSortStrategy implements ProductSortStrategy
{
    public function apply(Builder $query): Builder { return $query->latest(); }
    public function code(): string  { return 'newest'; }
    public function label(): string { return 'Mới nhất'; }
}

class PriceAscSortStrategy implements ProductSortStrategy
{
    public function apply(Builder $query): Builder { return $query->orderBy('price', 'asc'); }
    public function code(): string  { return 'priceAsc'; }
    public function label(): string { return 'Giá tăng dần'; }
}

class PriceDescSortStrategy implements ProductSortStrategy
{
    public function apply(Builder $query): Builder { return $query->orderBy('price', 'desc'); }
    public function code(): string  { return 'priceDesc'; }
    public function label(): string { return 'Giá giảm dần'; }
}

class NameAscSortStrategy implements ProductSortStrategy
{
    public function apply(Builder $query): Builder { return $query->orderBy('name', 'asc'); }
    public function code(): string  { return 'nameAsc'; }
    public function label(): string { return 'Tên A → Z'; }
}

class NameDescSortStrategy implements ProductSortStrategy
{
    public function apply(Builder $query): Builder { return $query->orderBy('name', 'desc'); }
    public function code(): string  { return 'nameDesc'; }
    public function label(): string { return 'Tên Z → A'; }
}

/** Factory gọi Strategy phù hợp theo mã sắp xếp */
class ProductSortStrategyFactory
{
    public static function make(string $order): ProductSortStrategy
    {
        return match ($order) {
            'priceAsc'  => new PriceAscSortStrategy(),
            'priceDesc' => new PriceDescSortStrategy(),
            'nameAsc'   => new NameAscSortStrategy(),
            'nameDesc'  => new NameDescSortStrategy(),
            default     => new NewestSortStrategy(),
        };
    }

    /** @return ProductSortStrategy[] */
    public static function all(): array
    {
        return [
            new NewestSortStrategy(),
            new PriceAscSortStrategy(),
            new PriceDescSortStrategy(),
            new NameAscSortStrategy(),
            new NameDescSortStrategy(),
        ];
    }
}

class ProductService
{
    public function listByCategory(
        ?int $categoryId,
        string $order,
        ?float $fromPrice = null,
        ?float $toPrice = null
    ): LengthAwarePaginator {
        $query = Product::query();

        if ($categoryId) {
            $query->inCategory($categoryId);
        }

        if ($fromPrice !== null) {
            $query->where('price', '>=', $fromPrice);
        }

        if ($toPrice !== null) {
            $query->where('price', '<=', $toPrice);
        }

        // Strategy Pattern: áp dụng chiến lược sắp xếp
        $query = ProductSortStrategyFactory::make($order)->apply($query);

        return $query->paginate(12)->withQueryString();
    }

    public function search(string $keyword, ?float $fromPrice, ?float $toPrice): LengthAwarePaginator
    {
        $query = Product::query();

        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        if ($fromPrice !== null) {
            $query->where('price', '>=', $fromPrice);
        }

        if ($toPrice !== null) {
            $query->where('price', '<=', $toPrice);
        }

        return $query->latest()->paginate(12)->withQueryString();
    }
}
