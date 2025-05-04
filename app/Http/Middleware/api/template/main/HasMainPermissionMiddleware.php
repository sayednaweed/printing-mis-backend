<?php

namespace App\Http\Middleware\api\template\main;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class HasMainPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission = null, $column = null): Response
    {
        $authUser = $request->user();
        if ($authUser) {
            // 1. Check user has user permission
            $permission = DB::table('user_permissions')->where("user_id", "=", $authUser->id)
                ->where("permission", '=', $permission)
                ->where($column, true)
                ->select('id')
                ->first();
            if ($permission) {
                return $next($request);
            }
        }
        return response()->json([
            'message' => __('app_translation.unauthorized'),
        ], 403, [], JSON_UNESCAPED_UNICODE);
    }
}
