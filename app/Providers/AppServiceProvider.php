<?php

namespace App\Providers;

use App\Models\UserGroup;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Module; // assuming you have Module model
use App\Models\ModuleGroup;
use App\View\Composers\ClientMenuComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Attach sidebar data to all views or a specific view
        View::composer('layouts.navigation', function ($view) {
            $userGroup = auth()->user()->user_group; // assuming 'user_group' on users table
            $moduleList = UserGroup::findOrFail($userGroup)->modules;
            $modules = explode(",", $moduleList);

            $module_groups = ModuleGroup::where('panel_type', 'admin')
                ->orderBy('module_group_order')
                ->with(['modules' => function ($query) use ($modules) {
                    $query->whereIn('id', $modules)
                        ->orderBy('module_order');
                }])
                ->get()
                ->map(function ($group) {
                    return [
                        'module_group_name' => $group->module_group_name,
                        'module_group_code' => $group->module_group_code,
                        'modules' => $group->modules->map(function ($module) {
                            return [
                                'modules_name' => $module->modules_name,
                                'module_url'   => $module->module_url,
                            ];
                        })->values()
                    ];
                });

            // Pass to view
            $view->with([
                'moduleGroups' => $module_groups,
                'breadcrumbModuleUrl' => request()->path(), // current route path
                'leftMenuModuleUrl' => request()->path(),
            ]);
        });

        View::composer('client.*', function ($view) {
            $view->with('leftMenuModuleUrl', request()->path());
        });
    }
}
