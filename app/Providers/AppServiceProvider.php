<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 本番環境（Heroku）で常時SSL（https）化する設定
        if (App::environment(['production'])) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $hour = \Carbon\Carbon::now('Asia/Tokyo')->hour;

            // 21:00-4:59 はここで先に拾う（0〜4時をこんにちはにしないため）
            if ($hour < 5 || $hour >= 21) {
                $greeting = 'おつかれさま';      // 21:00-4:59
            } elseif ($hour < 11) {
                $greeting = 'おはよう';          // 5:00-10:59
            } elseif ($hour < 17) {
                $greeting = 'こんにちは';        // 11:00-16:59
            } else {
                $greeting = 'こんばんは';        // 17:00-20:59
            }

            $view->with('greeting', $greeting);
        });
    }
}
