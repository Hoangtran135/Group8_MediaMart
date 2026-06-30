<?php

namespace App\Http\Controllers;

use App\Services\WishlistService;

class WishlistController extends Controller
{
    private WishlistService $wishlistService;

    public function __construct()
    {
        // Singleton: lấy instance duy nhất thay vì inject mới
        $this->wishlistService = WishlistService::getInstance();
    }

    public function index()
    {
        $wishlist = $this->wishlistService->get();

        return view('frontend.wishlist', compact('wishlist'));
    }

    public function add(int $id)
    {
        $this->wishlistService->add($id);

        return redirect()->back()->with('success', 'Đã thêm vào danh sách yêu thích!');
    }

    public function remove(int $id)
    {
        $this->wishlistService->remove($id);

        return redirect()->route('wishlist.index')->with('success', 'Đã xóa khỏi danh sách yêu thích!');
    }
}
