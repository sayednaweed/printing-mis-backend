<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enums\RoleEnum;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Traits\Address\AddressTrait;
use Illuminate\Support\Facades\Http;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Permission\PermissionRepositoryInterface;

class TestController extends Controller
{
    protected $userRepository;
    use HelperTrait;
    protected $permissionRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->userRepository = $userRepository;
    }

    private function detectDevice($userAgent)
    {
        if (str_contains($userAgent, 'Windows')) return 'Windows PC';
        if (str_contains($userAgent, 'Macintosh')) return 'Mac';
        if (str_contains($userAgent, 'iPhone')) return 'iPhone';
        if (str_contains($userAgent, 'Android')) return 'Android Device';
        return 'Unknown Device';
    }

    private function getLocationFromIP($ip)
    {

        try {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            return $response->json()['city'] . ', ' . $response->json()['country'];
        } catch (\Exception $e) {
            return 'Unknown Location';
        }
    }
    use AddressTrait;

    public function format($approvals)
    {
        return $approvals->groupBy('id')->map(function ($group) {
            $docs = $group->filter(function ($item) {
                return $item->approval_document_id !== null;
            });

            $approval = $group->first();

            $approval->approved = (bool) $approval->approved;
            if ($docs->isNotEmpty()) {
                $docs->documents = $docs->map(function ($doc) {
                    return [
                        'id' => $doc->approval_document_id,
                        'documentable_id' => $doc->documentable_id,
                        'documentable_type' => $doc->documentable_type,
                    ];
                });
            } else {
                $approval->documents = [];
            }
            unset($approval->approval_document_id);

            return $approval;
        })->values();
    }
    function extractDeviceInfo($userAgent)
    {
        // Match OS and architecture details
        if (preg_match('/\(([^)]+)\)/', $userAgent, $matches)) {
            return $matches[1]; // Extract content inside parentheses
        }
        return "Unknown Device";
    }
    function extractBrowserInfo($userAgent)
    {
        // Match major browsers (Chrome, Firefox, Safari, Edge, Opera, etc.)
        if (preg_match('/(Chrome|Firefox|Safari|Edge|Opera|OPR|MSIE|Trident)[\/ ]([\d.]+)/', $userAgent, $matches)) {
            $browser = $matches[1];
            $version = $matches[2];

            // Fix for Opera (uses "OPR" in User-Agent)
            if ($browser == 'OPR') {
                $browser = 'Opera';
            }

            // Fix for Internet Explorer (uses "Trident" in newer versions)
            if ($browser == 'Trident') {
                preg_match('/rv:([\d.]+)/', $userAgent, $rvMatches);
                $version = $rvMatches[1] ?? $version;
                $browser = 'Internet Explorer';
            }

            return "$browser $version";
        }

        return "Unknown Browser";
    }
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $expenseTypeId = 1;
        $type = DB::table('expense_type_trans as ett')
            ->where('ett.expense_type_id', $expenseTypeId)
            ->select(
                'ett.expense_type_id as id',
                DB::raw("MAX(CASE WHEN ett.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN ett.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN ett.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('ett.expense_type_id')
            ->first();
        $icons = DB::table('icons as i')
            ->leftJoin('expense_type_icons as eti', function ($join) use ($expenseTypeId) {
                $join->on('eti.icon_id', '=', 'i.id')
                    ->where('eti.expense_type_id', $expenseTypeId);
            })
            ->leftJoin('icon_trans as it', function ($join) use ($locale) {
                $join->on('it.icon_id', '=', 'i.id')
                    ->where('it.language_name', $locale);
            })
            ->select(
                'i.id',
                'it.value as name',
                'i.path',
                DB::raw('CASE WHEN eti.icon_id IS NOT NULL THEN true ELSE false END as selected')
            )
            ->get();
        return response()->json([
            'expenseType' => $type,
            'icons' => $icons
        ], 201);

        $attendanceTime = DB::table('application_configurations as ac')
            ->where('ac.id', 1)
            ->select(
                'ac.id',
                'ac.attendance_check_in_time',
                'ac.attendance_check_out_time',
            )->first();

        $checkInTime  = Carbon::createFromTimeString($attendanceTime->attendance_check_in_time);
        $checkOutTime  = Carbon::createFromTimeString($attendanceTime->attendance_check_out_time);
        // 3.1. If It Is above 12:00AM and Below Check-in time do not allow
        $currentTime = Carbon::now()->format('H:i:s'); // 24-hour format
        $currentTime = '03:00:00'; // 12:00 AM
        $startTime = '00:00:00'; // 12:00 AM
        $endTime = '08:00:00';   // 8:00 AM

        // return $currentTime >= $startTime && $currentTime <= $endTime;
        return response()->json([
            'data' => $currentTime >= $startTime,
            'new' =>  $currentTime <= $endTime,
        ], 200, [], JSON_UNESCAPED_UNICODE);

        if (
            $now->format('H:i:s') >= Carbon::createFromTime(16, 0, 0)->format('H:i:s')
            && $now->format('H:i:s') < $checkInTime->format('H:i:s')
        ) {
            return 'You are not allowed.';
        } else {
            return 'You are allowed.' . $now->format('H:i:s');
        }

        $column = 'edit';
        $perm = HrPermissionEnum::employees->value;
        $permSub = SubPermissionEnum::hr_employees_information->value;
        $permission = DB::table("user_permissions as up")
            ->where("user_id", "=", 1)
            ->where("permission", $perm)
            ->join("user_permission_subs as ups", function ($join) use ($permSub, &$column) {
                return $join->on('ups.user_permission_id', '=', 'up.id')
                    ->where('ups.sub_permission_id', $permSub)
                    ->where("ups." . $column, true);
            })->select("ups.id")->first();

        return $permission;
        $user_id = RoleEnum::super->value;

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
                'p.priority',
                'p.portal',
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

        return $formattedPermissions;


        // $this->command->info('Vaccine centers imported successfully!');
    }
}
