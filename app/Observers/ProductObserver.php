<?php


namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    public function deleting(Product $product): void
    {
        if ($product->photo) {
            Storage::disk('uploads')->delete('products/' . $product->photo);
        }
    }
}
