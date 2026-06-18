<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CheckModulePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $moduleUrl): Response
    {
        $module = DB::table('modules')
            ->where('module_url', $moduleUrl)
            ->first();

        if (!$module) {
            abort(403);
        }

        $userGroup = Session::get('user_group');

        $group = DB::table('user_group')
            ->where('id', $userGroup)
            ->first();

        $moduleArr = explode(',', $group->modules);

        if (!in_array($module->id, $moduleArr)) {
            abort(403, 'Unauthorized Access');
        }

        return $next($request);
    }
}
