<?php


namespace App\Observers;

use App\Models\NewsArticle;
use Illuminate\Support\Facades\Storage;

class NewsArticleObserver
{
    public function deleting(NewsArticle $article): void
    {
        if ($article->photo) {
            Storage::disk('uploads')->delete('news/' . $article->photo);
        }
    }
}
