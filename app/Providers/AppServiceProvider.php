<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bagikan SiteSetting (singleton) ke semua view sebagai $site.
        // Aman terhadap state pre-migration (saat install).
        View::composer('*', function ($view) {
            $site = null;
            try {
                if (Schema::hasTable('site_settings')) {
                    $site = SiteSetting::current();
                }
            } catch (\Throwable $e) {
                $site = null;
            }
            $view->with('site', $site);
        });
    }
}
