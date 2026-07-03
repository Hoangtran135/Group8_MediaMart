<?php


namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\SendOrderEmailNotification;
use App\Listeners\SendOrderSmsNotification;
use App\Models\NewsArticle;
use App\Models\Product;
use App\Observers\NewsArticleObserver;
use App\Observers\ProductObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Product::observe(ProductObserver::class);
        NewsArticle::observe(NewsArticleObserver::class);

        Event::listen(OrderPlaced::class, SendOrderEmailNotification::class);
        Event::listen(OrderPlaced::class, SendOrderSmsNotification::class);
    }
}
