<?php

namespace App\Providers;

use App\Models\Web\Notices;
use App\Services\PrayerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class WebComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
{
    View::composer('*', function ($view) {

        // Footer slider images
        // $footerSliderImage = DB::table('web_footer_image')
        //     ->select('image')
        //     ->orderBy('image_order', 'ASC')
        //     ->get()
        //     ->toArray();

        // // Active notices
        // $noticeLists = Notices::where('is_active', 1)
        //     ->orderBy('updated_dt_tm', 'DESC')
        //     ->get();

        // // Prayer time
        // $prayerTime = function () {
        //     return app(PrayerService::class)->getPrayerTime();
        // };

        // // Pass variables to all views
        // $view->with([
        //     'footerSliderImage' => $footerSliderImage,
        //     'noticeLists' => $noticeLists,
        //     'prayerTime' => $prayerTime(), // call function
        // ]);
    });
}
}
