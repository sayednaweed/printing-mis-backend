<?php

namespace App\Http\Controllers\api\template;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function roles(Request $request)
    {
        $role_id = $request->user()->role_id;
        $includeRole = [];

        if ($role_id === RoleEnum::super->value) {
            array_push($includeRole, RoleEnum::admin->value);
            array_push($includeRole, RoleEnum::user->value);
        } else {
            array_push($includeRole, RoleEnum::user->value);
        }

        $tr = Role::whereIn('id', $includeRole)->select("name", 'id')->get();
        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
