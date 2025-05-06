<?php

namespace App\Http\Controllers\api\app\hr\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // protected $pendingTaskRepository;
    // protected $storageRepository;
    // protected $permissionRepository;

    // public function __construct(
    //     PendingTaskRepositoryInterface $pendingTaskRepository,
    //     StorageRepositoryInterface $storageRepository,
    //     PermissionRepositoryInterface $permissionRepository
    // ) {
    //     $this->pendingTaskRepository = $pendingTaskRepository;
    //     $this->storageRepository = $storageRepository;
    //     $this->permissionRepository = $permissionRepository;
    // }

    // public function user($id)
    // {
    //     $locale = App::getLocale();
    //     $user = DB::table('epi_users as eu')
    //         ->where('eu.id', $id)
    //         ->join('model_job_trans as mjt', function ($join) use ($locale) {
    //             $join->on('mjt.model_job_id', '=', 'eu.job_id')
    //                 ->where('mjt.language_name', $locale);
    //         })
    //         ->leftJoin('contacts as c', 'c.id', '=', 'eu.contact_id')
    //         ->join('emails as e', 'e.id', '=', 'eu.email_id')
    //         ->join('roles as r', 'r.id', '=', 'eu.role_id')
    //         ->join('genders as g', 'g.id', '=', 'eu.gender_id')
    //         ->join('destination_trans as dt', function ($join) use ($locale) {
    //             $join->on('dt.destination_id', '=', 'eu.destination_id')
    //                 ->where('dt.language_name', $locale);
    //         })
    //         ->leftJoin('zone_trans as zt', function ($join) use ($locale) {
    //             $join->on('zt.zone_id', '=', 'eu.zone_id')
    //                 ->where('zt.language_name', $locale);
    //         })
    //         ->leftJoin('province_trans as prot', function ($join) use ($locale) {
    //             $join->on('prot.province_id', '=', 'eu.province_id')
    //                 ->where('zt.language_name', $locale);
    //         })
    //         ->select(
    //             'eu.id',
    //             'eu.registeration_number',
    //             "eu.profile",
    //             "eu.status",
    //             'eu.full_name',
    //             'eu.username',
    //             'c.value as contact',
    //             'eu.contact_id',
    //             'e.value as email',
    //             'eu.email_id',
    //             'r.name as role_name',
    //             'eu.role_id',
    //             'dt.value as destination',
    //             'zt.value as zone',
    //             'prot.value as province',
    //             "mjt.value as job",
    //             "eu.created_at",
    //             "eu.province_id",
    //             "eu.destination_id",
    //             "eu.job_id",
    //             "eu.zone_id",
    //             "g.id as gender_id",
    //             "g.name_en",
    //             "g.name_ps",
    //             "g.name_fa",

    //         )
    //         ->first();
    //     if (!$user) {
    //         return response()->json([
    //             'message' => __('app_translation.user_not_found'),
    //         ], 404, [], JSON_UNESCAPED_UNICODE);
    //     }
    //     $gender = $user->name_en;
    //     if ($locale == LanguageEnum::farsi->value) {
    //         $gender = $user->name_fa;
    //     } else if ($locale == LanguageEnum::pashto->value) {
    //         $gender = $user->name_ps;
    //     }


    //     return response()->json(
    //         [
    //             "user" => [
    //                 "id" => $user->id,
    //                 "registration_number" => $user->registeration_number,
    //                 "full_name" => $user->full_name,
    //                 "username" => $user->username,
    //                 'email' => $user->email,
    //                 "profile" => $user->profile,
    //                 "status" => $user->status == 1,
    //                 "role" => ['id' => $user->role_id, 'name' => $user->role_name],
    //                 "zone" => ['id' => $user->zone_id, 'name' => $user->zone],
    //                 "gender" => ['id' => $user->gender_id, 'name' => $gender],
    //                 "province" => ['id' => $user->province_id, 'name' => $user->province],
    //                 'contact' => $user->contact,
    //                 "destination" => ["id" => $user->destination_id, "name" => $user->destination],
    //                 "job" => ["id" => $user->job_id, "name" => $user->job],
    //                 "created_at" => $user->created_at,
    //             ],
    //         ],
    //         200,
    //         [],
    //         JSON_UNESCAPED_UNICODE
    //     );
    // }
    // public function users(Request $request)
    // {
    //     $locale = App::getLocale();
    //     $tr = [];
    //     $perPage = $request->input('per_page', 10); // Number of records per page
    //     $page = $request->input('page', 1); // Current page
    //     $role_id =  $request->user()->role_id;
    //     $includeRole = [];
    //     $includeZone = [];

    //     if ($role_id === RoleEnum::epi_super->value) {
    //         array_push($includeRole, RoleEnum::epi_admin->value);
    //         array_push($includeRole, RoleEnum::epi_user->value);
    //     } else if ($role_id === RoleEnum::epi_admin->value) {
    //         array_push($includeRole, RoleEnum::epi_user->value);
    //         // zone
    //         array_push($includeZone, $request->user()->zone_id);
    //     } else {
    //         return response()->json([
    //             'message' => __('app_translation.unauthorized'),
    //         ], 401, [], JSON_UNESCAPED_UNICODE);
    //     }


    //     // Start building the query
    //     $query = DB::table('epi_users as eu')
    //         ->whereIn('eu.role_id', $includeRole)
    //         ->when(!empty($includeZone), function ($q) use ($includeZone) {
    //             $q->whereIn('eu.zone_id', $includeZone);
    //         })
    //         ->join('contacts as c', 'c.id', '=', 'eu.contact_id')
    //         ->join('emails as e', 'e.id', '=', 'eu.email_id')
    //         ->join('roles as r', 'r.id', '=', 'eu.role_id')
    //         ->leftjoin('destination_trans as dt', function ($join) use ($locale) {
    //             $join->on('dt.destination_id', '=', 'eu.destination_id')
    //                 ->where('dt.language_name', $locale);
    //         })
    //         ->join('zone_trans as zt', function ($join) use ($locale) {
    //             $join->on('zt.zone_id', '=', 'eu.zone_id')
    //                 ->where('zt.language_name', $locale);
    //         })
    //         ->join('model_job_trans as mjt', function ($join) use ($locale) {
    //             $join->on('mjt.model_job_id', '=', 'eu.job_id')
    //                 ->where('mjt.language_name', $locale);
    //         })
    //         ->select(
    //             "eu.id",
    //             "eu.registeration_number",
    //             "eu.full_name",
    //             "eu.username",
    //             "eu.profile",
    //             "eu.created_at",
    //             "eu.status",
    //             "e.value AS email",
    //             "c.value AS contact",
    //             "zt.value AS zone",
    //             "dt.value as destination",
    //             "mjt.value as job"
    //         );

    //     $this->applyDate($query, $request);
    //     $this->applyFilters($query, $request);
    //     $this->applySearch($query, $request);

    //     // Apply pagination (ensure you're paginating after sorting and filtering)
    //     $tr = $query->paginate($perPage, ['*'], 'page', $page);
    //     return response()->json(
    //         [
    //             "users" => $tr,
    //         ],
    //         200,
    //         [],
    //         JSON_UNESCAPED_UNICODE
    //     );
    // }

    // public function store(EpiUserStoreRequest $request)
    // {
    //     $validatedData = $request->validated();
    //     $request->validate([
    //         'zone' => 'required',
    //         'job' => 'required',
    //         'destination' => 'required',
    //     ]);
    //     $role_id = $request->role_id;
    //     $zone_id = $request->zone_id;
    //     if ($request->user()->role_id != RoleEnum::epi_super->value) {
    //         $role_id = RoleEnum::epi_user->value;
    //         $zone_id = $request->user()->zone_id;
    //     } else {
    //         $request->validate([
    //             'role_id' => 'required|exists:roles,id',
    //             'zone_id' => 'required|exists:zones,id',
    //         ]);
    //     }

    // // Create email
    // $email = Email::where('value', '=', $request->email)->first();
    // if ($email) {
    //     return response()->json([
    //         'message' => __('app_translation.email_exist'),
    //     ], 400, [], JSON_UNESCAPED_UNICODE);
    // }
    // // 2. Check contact
    // $contact = null;
    // if ($request->contact !== null && !empty($request->contact)) {
    //     $contact = Contact::where('value', '=', $request->contact)->first();
    //     if ($contact) {
    //         return response()->json([
    //             'message' => __('app_translation.contact_exist'),
    //         ], 400, [], JSON_UNESCAPED_UNICODE);
    //     }
    //     $contact = Contact::create([
    //         "value" => $request->contact
    //     ]);
    // }
    //     DB::beginTransaction();
    //     // Add email and contact
    //     $email = Email::create([
    //         "value" => $request->email
    //     ]);

    //     $user = EpiUser::create([
    //         "registeration_number" => '',
    //         "full_name" => $request->full_name,
    //         "username" => $request->username,
    //         "email_id" => $email->id,
    //         "password" => Hash::make($validatedData['password']),
    //         "status" => $request->status == true,
    //         "role_id" => $role_id,
    //         "contact_id" => $contact ? $contact->id : $contact,
    //         "zone_id" => $zone_id,
    //         "province_id" => $request->province_id,
    //         "gender_id" => $request->gender_id,
    //         "job_id" => $request->job_id,
    //         "destination_id" => $request->destination_id,
    //     ]);
    //     $user->registeration_number = "EPI-" . Carbon::now()->year . '-' . $user->id;

    //     $task = $this->pendingTaskRepository->pendingTaskExist(
    //         $request->user(),
    //         CheckListTypeEnum::epi->value,
    //         CheckListEnum::epi_user_letter_of_introduction->value,
    //         null
    //     );

    //     if (!$task) {
    //         return response()->json([
    //             'message' => __('app_translation.task_not_found')
    //         ], 404);
    //     }
    //     $document_id = '';

    //     $this->storageRepository->documentStore(CheckListTypeEnum::epi->value, $user->id, $task->id, function ($documentData) use (&$document_id) {
    //         $checklist_id = $documentData['check_list_id'];
    //         $document = Document::create([
    //             'actual_name' => $documentData['actual_name'],
    //             'size' => $documentData['size'],
    //             'path' => $documentData['path'],
    //             'type' => $documentData['type'],
    //             'check_list_id' => $checklist_id,
    //         ]);
    //         $document_id = $document->id;
    //     });
    //     $user->user_letter_of_introduction_id  = $document_id;
    //     $user->save();

    //     $this->pendingTaskRepository->destroyPendingTask(
    //         $request->user(),
    //         CheckListTypeEnum::epi->value,
    //         CheckListEnum::epi_user_letter_of_introduction->value,
    //         null
    //     );

    //     // 4. Add user permissions
    //     $result = $this->permissionRepository->storeEpiPermission($user, $request->permissions);
    //     if ($result == 400) {
    //         return response()->json([
    //             'message' => __('app_translation.user_not_found'),
    //         ], 404, [], JSON_UNESCAPED_UNICODE);
    //     } else if ($result == 401) {
    //         return response()->json([
    //             'message' => __('app_translation.unauthorized_role_per'),
    //         ], 403, [], JSON_UNESCAPED_UNICODE);
    //     } else if ($result == 402) {
    //         return response()->json([
    //             'message' => __('app_translation.per_not_found'),
    //         ], 404, [], JSON_UNESCAPED_UNICODE);
    //     }
    //     DB::commit();


    //     return response()->json(
    //         [
    //             "user" => [
    //                 "id" => $user->id,
    //                 "registeration_number" => $user->registeration_number,
    //                 "full_name" => $user->full_name,
    //                 "username" => $user->username,
    //                 "profile" => $user->profile,
    //                 "created_at" => $user->created_at,
    //                 "status" => $user->status,
    //                 "email" => $request->email,
    //                 "contact" => $request->contact,
    //                 "zone" => $request->zone,
    //                 "destination" => $request->destination,
    //                 "job" => $request->job,
    //             ],
    //             "message" => __('app_translation.success'),
    //         ],
    //         200,
    //         [],
    //         JSON_UNESCAPED_UNICODE
    //     );
    // }
    // public function updateInformation(UpdateUserRequest $request)
    // {
    //     $request->validated();
    //     $user = $request->get('validatedUser');
    //     // 1. User is passed from middleware
    //     DB::beginTransaction();
    //     if ($user) {
    // $email = Email::where('value', $request->email)
    //     ->select('id')->first();
    // // Email Is taken by someone
    // if ($email) {
    //     if ($email->id == $user->email_id) {
    //         $email->value = $request->email;
    //         $email->save();
    //     } else {
    //         return response()->json([
    //             'message' => __('app_translation.email_exist'),
    //         ], 409, [], JSON_UNESCAPED_UNICODE);
    //     }
    // } else {
    //     $email = Email::where('id', $user->email_id)->first();
    //     $email->value = $request->email;
    //     $email->save();
    // }
    // if ($request->contact !== null && !empty($request->contact)) {
    //     $contact = Contact::where('value', $request->contact)
    //         ->select('id')->first();
    //     if ($contact) {
    //         if ($contact->id == $user->contact_id) {
    //             $contact->value = $request->contact;
    //             $contact->save();
    //         } else {
    //             return response()->json([
    //                 'message' => __('app_translation.contact_exist'),
    //             ], 409, [], JSON_UNESCAPED_UNICODE);
    //         }
    //     } else {
    //         if (isset($user->contact_id)) {
    //             $contact = Contact::where('id', $user->contact_id)->first();
    //             $contact->value = $request->contact;
    //             $contact->save();
    //         } else {
    //             $contact = Contact::create(['value' => $request->contact]);
    //             $user->contact_id = $contact->id;
    //         }
    //     }
    // }

    //         // 4. Update User other attributes
    //         $user->full_name = $request->full_name;
    //         $user->username = $request->username;
    //         $user->job_id = $request->job_id;
    //         $user->destination_id = $request->destination_id;
    //         $user->province_id = $request->province_id;
    //         $user->gender_id = $request->gender_id;
    //         $user->zone_id = $request->zone_id;
    //         $user->status = $request->status == 'true';
    //         $user->save();

    //         DB::commit();
    //         return response()->json([
    //             'message' => __('app_translation.success'),
    //         ], 200, [], JSON_UNESCAPED_UNICODE);
    //     }
    //     return response()->json([
    //         'message' => __('app_translation.user_not_found'),
    //     ], 404, [], JSON_UNESCAPED_UNICODE);
    // }
    // public function updateProfilePicture(Request $request)
    // {
    //     $request->validate([
    //         'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
    //         'id' => 'required',
    //     ]);
    //     $user = EpiUser::find($request->id);
    //     if (!$user) {
    //         return response()->json([
    //             'message' => __('app_translation.user_not_found'),
    //         ], 404, [], JSON_UNESCAPED_UNICODE);
    //     }
    //     $path = $this->storeProfile($request, 'epi-profile');
    //     if ($path != null) {
    //         // 1. delete old profile
    //         $this->deleteDocument($this->getProfilePath($user->profile));
    //         // 2. Update the profile
    //         $user->profile = $path;
    //     }
    //     $user->save();
    //     return response()->json([
    //         'message' => __('app_translation.profile_changed'),
    //         "profile" => $user->profile
    //     ], 200, [], JSON_UNESCAPED_UNICODE);
    // }
    // public function deleteProfilePicture($id)
    // {
    //     $user = EpiUser::find($id);
    //     if (!$user) {
    //         return response()->json([
    //             'message' => __('app_translation.user_not_found'),
    //         ], 404, [], JSON_UNESCAPED_UNICODE);
    //     }
    //     // 1. delete old profile
    //     $this->deleteDocument($this->getProfilePath($user->profile));
    //     // 2. Update the profile
    //     $user->profile = null;
    //     $user->save();
    //     return response()->json([
    //         'message' => __('app_translation.success')
    //     ], 200, [], JSON_UNESCAPED_UNICODE);
    // }
    // public function changePassword(UpdateUserPasswordRequest $request)
    // {
    //     $request->validated();
    //     $user = $request->get('validatedUser');
    //     $authUser = $request->user();
    //     // Admin trying to change other zone user password
    //     if (($authUser->role_id == RoleEnum::epi_admin->value && $user->zone_id != $authUser->zone_id)
    //         || $authUser->role_id == RoleEnum::epi_user->value
    //     ) {
    //         // 1. Insert Fruad record

    //         // 2. Lock User
    //         $authUser->status = false;
    //         $authUser->save();
    //         return response()->json([
    //             'message' => __('app_translation.account_is_block'),
    //         ], 403, [], JSON_UNESCAPED_UNICODE);
    //     }

    //     // 1. Validate document
    //     $task = $this->pendingTaskRepository->pendingTaskExist(
    //         $request->user(),
    //         CheckListTypeEnum::epi->value,
    //         CheckListEnum::epi_letter_of_password_change->value,
    //         CheckListEnum::epi_letter_of_password_change->value,
    //     );
    //     if (!$task) {
    //         return response()->json([
    //             'message' => __('app_translation.task_not_found')
    //         ], 404);
    //     }
    //     DB::beginTransaction();
    //     $user->password = Hash::make($request->new_password);
    //     $user->save();
    //     $document_id = '';
    //     $this->storageRepository->documentStore(CheckListTypeEnum::epi->value, $user->id, $task->id, function ($documentData) use (&$document_id) {
    //         $checklist_id = $documentData['check_list_id'];
    //         $document = Document::create([
    //             'actual_name' => $documentData['actual_name'],
    //             'size' => $documentData['size'],
    //             'path' => $documentData['path'],
    //             'type' => $documentData['type'],
    //             'check_list_id' => $checklist_id,
    //         ]);
    //         $document_id = $document->id;
    //     });
    //     EpiUserPasswordChange::create([
    //         'target_user_id' => $authUser->id,
    //         'affected_user_id' => $user->id,
    //         'document_id' => $document_id,
    //     ]);
    //     DB::commit();
    //     return response()->json([
    //         'message' => __('app_translation.success'),
    //     ], 200, [], JSON_UNESCAPED_UNICODE);
    // }
    // protected function applyDate($query, $request)
    // {
    //     // Apply date filtering conditionally if provided
    //     $startDate = $request->input('filters.date.startDate');
    //     $endDate = $request->input('filters.date.endDate');

    //     if ($startDate) {
    //         $query->where('eu.created_at', '>=', $startDate);
    //     }
    //     if ($endDate) {
    //         $query->where('eu.created_at', '<=', $endDate);
    //     }
    // }
    // // search function 
    // protected function applySearch($query, $request)
    // {
    //     $searchColumn = $request->input('filters.search.column');
    //     $searchValue = $request->input('filters.search.value');

    //     if ($searchColumn && $searchValue) {
    //         $allowedColumns = [
    //             'registration_number' => 'eu.registeration_number',
    //             'username' => 'eu.username',
    //             'contact' => 'c.value',
    //             'email' => 'e.value',
    //             'zone' => 'zt.value',
    //         ];
    //         // Ensure that the search column is allowed
    //         if (in_array($searchColumn, array_keys($allowedColumns))) {
    //             $query->where($allowedColumns[$searchColumn], 'like', '%' . $searchValue . '%');
    //         }
    //     }
    // }
    // // filter function
    // protected function applyFilters($query, $request)
    // {
    //     $sort = $request->input('filters.sort'); // Sorting column
    //     $order = $request->input('filters.order', 'asc'); // Sorting order (default 
    //     $allowedColumns = [
    //         'username' => 'eu.username',
    //         'email' => 'e.value',
    //         'zone' => 'zt.value',
    //         'job' => 'mjt.value',
    //         'created_at' => 'eu.created_at',
    //         'destination' => 'dt.value'

    //     ];
    //     if (in_array($sort, array_keys($allowedColumns))) {
    //         $query->orderBy($allowedColumns[$sort], $order);
    //     }
    // }

    // public function userCount()
    // {
    //     $user = request()->user();
    //     $zone_id = null;

    //     if ($user->role_id == RoleEnum::epi_admin->value) {
    //         $zone_id = $user->zone_id;
    //     }
    //     $zoneFilter = $zone_id ? "WHERE zone_id = $zone_id" : "";
    //     $zoneFilter1 = $zone_id ? "AND zone_id = $zone_id" : "";
    //     $statistics = DB::select("
    //             select count(*) as userCount,
    //             (select count(*) from epi_users where DATE(created_at) = CURDATE() {$zoneFilter1}) AS todayCount,
    //             (select count(*) from epi_users where status = 1 {$zoneFilter1}) AS activeUserCount,
    //             (select count(*) from epi_users where status = 0 {$zoneFilter1}) AS inActiveUserCount
    //             from epi_users {$zoneFilter}
    //     ");

    //     return response()->json([
    //         'counts' => [
    //             "userCount" => $statistics[0]->userCount,
    //             "todayCount" => $statistics[0]->todayCount,
    //             "activeUserCount" => $statistics[0]->activeUserCount,
    //             "inActiveUserCount" =>  $statistics[0]->inActiveUserCount
    //         ],
    //     ], 200, [], JSON_UNESCAPED_UNICODE);
    // }
}
