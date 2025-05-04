<?php

namespace App\Repositories\User;

use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function userAuthFormattedPermissions($user_id)
    {
        $permissions = DB::table('users as u')
            ->where('u.id', $user_id)
            ->join('user_permissions as up', 'u.id', '=', 'up.user_id')
            ->join('permissions as p', function ($join) {
                $join->on('up.permission', '=', 'p.name')
                    ->where('up.view', true);
            })
            ->leftJoin('user_permission_subs as ups', function ($join) {
                $join->on('up.id', '=', 'ups.user_permission_id')
                    ->where('ups.view', true);
            })
            ->select(
                'up.id as user_permission_id',
                'p.name as permission',
                'p.icon',
                'p.portal',
                'p.priority',
                'up.view',
                'up.edit',
                'up.delete',
                'up.add',
                'up.visible',
                DB::raw('ups.sub_permission_id as sub_permission_id'),
                DB::raw('ups.add as sub_add'),
                DB::raw('ups.delete as sub_delete'),
                DB::raw('ups.edit as sub_edit'),
                DB::raw('ups.view as sub_view')
            )
            ->orderBy('p.priority')  // Optional: If you want to order by priority, else remove
            ->get();

        $formattedPermissions = $permissions->groupBy('portal')->map(function ($portalGroup) {
            return $portalGroup->groupBy('user_permission_id')->map(function ($group) {
                $subPermissions = $group->filter(function ($item) {
                    return $item->sub_permission_id !== null;
                });

                $permission = $group->first(); // Get the main permission data

                $permission->view = (bool) $permission->view;
                $permission->edit = (bool) $permission->edit;
                $permission->delete = (bool) $permission->delete;
                $permission->add = (bool) $permission->add;

                if ($subPermissions->isNotEmpty()) {
                    $permission->sub = $subPermissions->sortBy('sub_permission_id')->map(function ($sub) {
                        return [
                            'id' => $sub->sub_permission_id,
                            'add' => (bool) $sub->sub_add,
                            'delete' => (bool) $sub->sub_delete,
                            'edit' => (bool) $sub->sub_edit,
                            'view' => (bool) $sub->sub_view,
                        ];
                    })->values();
                } else {
                    $permission->sub = [];
                }

                // Cleanup unnecessary fields
                unset($permission->sub_permission_id);
                unset($permission->sub_add);
                unset($permission->sub_delete);
                unset($permission->sub_edit);

                return $permission;
            })->values();
        });
        // Transform data to match desired structure (for example, if you need nested `sub` permissions)
        // $formattedPermissions = $permissions->groupBy('user_permission_id')->map(function ($group) {
        //     $subPermissions = $group->filter(function ($item) {
        //         return $item->sub_permission_id !== null; // Filter for permissions that have sub-permissions
        //     });

        //     $permission = $group->first(); // Get the first permission for this group

        //     $permission->view = (bool) $permission->view;
        //     $permission->edit = (bool) $permission->edit;
        //     $permission->delete = (bool) $permission->delete;
        //     $permission->add = (bool) $permission->add;
        //     $permission->visible = (bool) $permission->visible;
        //     if ($subPermissions->isNotEmpty()) {
        //         $permission->sub = $subPermissions->sortBy('sub_permission_id')->map(function ($sub) {
        //             return [
        //                 'id' => $sub->sub_permission_id,
        //                 'add' => (bool) $sub->sub_add,
        //                 'delete' => (bool) $sub->sub_delete,
        //                 'edit' => (bool) $sub->sub_edit,
        //                 'view' => (bool) $sub->sub_view,
        //             ];
        //         })->values();
        //     } else {
        //         $permission->sub = [];
        //     }
        //     // If there are no sub-permissions, remove the unwanted fields
        //     unset($permission->sub_permission_id);
        //     unset($permission->sub_add);
        //     unset($permission->sub_delete);
        //     unset($permission->sub_edit);

        //     return $permission;
        // })->values();

        return $formattedPermissions;
    }

    // public function epiAuthFormattedPermissions($finance_user_id)
    // {
    //     $permissions = DB::table('epi_users as eu')
    //         ->where('eu.id', $finance_user_id)
    //         ->join('epi_permissions as ep', 'eu.id', '=', 'ep.epi_user_id')
    //         ->join('permissions as p', function ($join) {
    //             $join->on('ep.permission', '=', 'p.name')
    //                 ->where('ep.view', true);
    //         })
    //         ->leftJoin('epi_permission_subs as eps', function ($join) {
    //             $join->on('ep.id', '=', 'eps.epi_permission_id')
    //                 ->where('eps.view', true);
    //         })
    //         ->select(
    //             'ep.id as epi_permission_id',
    //             'p.name as permission',
    //             'p.icon',
    //             'p.priority',
    //             'ep.view',
    //             'ep.edit',
    //             'ep.delete',
    //             'ep.add',
    //             'ep.visible',
    //             DB::raw('eps.sub_permission_id as sub_permission_id'),
    //             DB::raw('eps.add as sub_add'),
    //             DB::raw('eps.delete as sub_delete'),
    //             DB::raw('eps.edit as sub_edit'),
    //             DB::raw('eps.view as sub_view')
    //         )
    //         ->orderBy('p.priority')  // Optional: If you want to order by priority, else remove
    //         ->get();

    //     // Transform data to match desired structure (for example, if you need nested `sub` permissions)
    //     $formattedPermissions = $permissions->groupBy('epi_permission_id')->map(function ($group) {
    //         $subPermissions = $group->filter(function ($item) {
    //             return $item->sub_permission_id !== null; // Filter for permissions that have sub-permissions
    //         });

    //         $permission = $group->first(); // Get the first permission for this group

    //         $permission->view = (bool) $permission->view;
    //         $permission->edit = (bool) $permission->edit;
    //         $permission->delete = (bool) $permission->delete;
    //         $permission->add = (bool) $permission->add;
    //         $permission->visible = (bool) $permission->visible;
    //         if ($subPermissions->isNotEmpty()) {
    //             $permission->sub = $subPermissions->sortBy('sub_permission_id')->map(function ($sub) {
    //                 return [
    //                     'id' => $sub->sub_permission_id,
    //                     'add' => (bool) $sub->sub_add,
    //                     'delete' => (bool) $sub->sub_delete,
    //                     'edit' => (bool) $sub->sub_edit,
    //                     'view' => (bool) $sub->sub_view,
    //                 ];
    //             })->values();
    //         } else {
    //             $permission->sub = [];
    //         }
    //         // If there are no sub-permissions, remove the unwanted fields
    //         unset($permission->sub_permission_id);
    //         unset($permission->sub_add);
    //         unset($permission->sub_delete);
    //         unset($permission->sub_edit);

    //         return $permission;
    //     })->values();

    //     return $formattedPermissions;
    // }
}
