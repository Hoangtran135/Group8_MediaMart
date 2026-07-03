<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\NewsArticle;
use App\Services\ImageUploadService;

class AdminNewsController extends Controller
{
    public function __construct(private ImageUploadService $imageService) {}

    public function index()
    {
        $articles = NewsArticle::latest()->paginate(25);

        return view('admin.news.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.news.form');
    }

    public function store(NewsRequest $request)
    {
        $data        = $request->validated();
        $data['hot'] = $request->boolean('hot');

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->imageService->upload($request->file('photo'), 'news');
        }

        NewsArticle::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Thêm tin tức thành công!');
    }

    public function edit(int $id)
    {
        $article = NewsArticle::findOrFail($id);

        return view('admin.news.form', compact('article'));
    }

    public function update(NewsRequest $request, int $id)
    {
        $article     = NewsArticle::findOrFail($id);
        $data        = $request->validated();
        $data['hot'] = $request->boolean('hot');

        if ($request->hasFile('photo')) {
            if ($article->photo) {
                $this->imageService->delete('news', $article->photo);
            }
            $data['photo'] = $this->imageService->upload($request->file('photo'), 'news');
        }

        $article->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Cập nhật tin tức thành công!');
    }

    public function destroy(int $id)
    {
        // Observer tự xóa ảnh khi deleting
        NewsArticle::findOrFail($id)->delete();

        return redirect()->route('admin.news.index')->with('success', 'Đã xóa tin tức!');
    }
}
