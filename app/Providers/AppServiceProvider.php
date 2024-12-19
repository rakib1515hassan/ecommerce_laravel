<?php

namespace App\Providers;

ini_set('memory_limit', '-1');

use App\Services\AdditionalServices;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Amirami\Localizator\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
//        dd(config('cors.allowed_origins'));
        try {

            Paginator::useBootstrap();

            $web = BusinessSetting::all();
            $settings = AdditionalServices::get_settings($web, 'colors');
            $data = json_decode($settings['value'], true);
            $web_config = [
                'primary_color' => $data['primary'],
                'secondary_color' => $data['secondary'],
                'name' => AdditionalServices::get_settings($web, 'company_name'),
                'phone' => AdditionalServices::get_settings($web, 'company_phone'),
                'web_logo' => AdditionalServices::get_settings($web, 'company_web_logo'),
                'mob_logo' => AdditionalServices::get_settings($web, 'company_mobile_logo'),
                'fav_icon' => AdditionalServices::get_settings($web, 'company_fav_icon'),
                'email' => AdditionalServices::get_settings($web, 'company_email'),
                'about' => AdditionalServices::get_settings($web, 'about_us'),
                'footer_logo' => AdditionalServices::get_settings($web, 'company_footer_logo'),
                'copyright_text' => AdditionalServices::get_settings($web, 'company_copyright_text'),
            ];

            //language
            $language = BusinessSetting::where('type', 'language')->first();

            //currency
            \App\Services\AdditionalServices::currency_load();

            View::share(['web_config' => $web_config, 'language' => $language]);

            Schema::defaultStringLength(191);

            if (env('APP_ENV') !== 'local') {
                URL::forceScheme('https');
            }
        } catch (\Exception $ex) {
        }
    }
}
