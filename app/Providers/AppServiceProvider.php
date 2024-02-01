<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\View\Components\Modal;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Modal::closedByClickingAway(false);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['id','en']); // also accepts a closure
        });
    }
}
