<?php

namespace App\Http\Controllers\api\template;

use App\Models\Email;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Requests\template\user\ProfileUpdateRequest;

class ProfileController extends Controller
{
    use HelperTrait;

    public function deleteProfilePicture(Request $request)
    {
        $authUser = $request->user();
        // 1. delete old profile
        $this->deleteDocument($this->getProfilePath($authUser->profile));
        // 2. Update the profile
        $authUser->profile = null;
        $authUser->save();
        return response()->json([
            'message' => __('app_translation.success')
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function updateUserPicture(Request $request)
    {
        $request->validate([
            'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        return $this->savePicture($request, 'user-profile');
    }

    public function updateFinancePicture(Request $request)
    {
        $request->validate([
            'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        return $this->savePicture($request, 'epi-profile');
    }
    public function updateEpiPicture(Request $request)
    {
        $request->validate([
            'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        return $this->savePicture($request, 'epi-profile');
    }
    public function savePicture(Request $request, $dynamic_path)
    {
        $authUser = $request->user();
        $path = $this->storeProfile($request, $dynamic_path);
        if ($path != null) {
            // 1. delete old profile
            $this->deleteDocument($this->getProfilePath($authUser->profile));
            // 2. Update the profile
            $authUser->profile = $path;
        }
        $authUser->save();
        return response()->json([
            'message' => __('app_translation.profile_changed'),
            "profile" => $authUser->profile
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function updateUserProfileInfo(ProfileUpdateRequest $request)
    {
        $request->validated();
        $authUser = $request->user();
        // Begin transaction
        DB::beginTransaction();
        // 2. Get Email
        $email = Email::where('value', $request->email)
            ->select('id')->first();
        // Email Is taken by someone
        if ($email) {
            if ($email->id != $authUser->email_id) {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $email = Email::where('id', $authUser->email_id)->first();
            $email->value = $request->email;
            $email->save();
        }
        $contact = Contact::where('value', $request->contact)
            ->select('id')->first();
        if ($contact) {
            if ($contact->id != $authUser->contact_id) {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            if ($request->contact !== null && !empty($request->contact)) {
                if ($authUser->contact_id) {
                    $contact = Contact::where('id', $authUser->contact_id)->first();
                    $contact->value = $request->contact;
                    $contact->save();
                } else {
                    $contact = Contact::create([
                        'value' => $request->contact
                    ]);
                    $authUser->contact_id = $contact->id;
                }
            } else if ($authUser->contact_id) {
                Contact::where('id', $authUser->contact_id)->delete();
            }
        }
        $authUser->full_name = $request->full_name;
        $authUser->username = $request->username;
        $authUser->save();
        DB::commit();

        return response()->json([
            'message' => __('app_translation.profile_changed'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function updateProfileInfo(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:45'],
            'full_name' => ['required', 'string', 'max:45'],
            'id' => ['required', 'string'],
        ]);
        $authUser = $request->user();
        // Begin transaction
        DB::beginTransaction();
        $contact = Contact::where('value', $request->contact)
            ->select('id')->first();
        if ($contact) {
            if ($contact->id != $authUser->contact_id) {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            if ($request->contact !== null && !empty($request->contact)) {
                if ($authUser->contact_id) {
                    $contact = Contact::where('id', $authUser->contact_id)->first();
                    $contact->value = $request->contact;
                    $contact->save();
                } else {
                    $contact = Contact::create([
                        'value' => $request->contact
                    ]);
                    $authUser->contact_id = $contact->id;
                }
            } else if ($authUser->contact_id) {
                Contact::where('id', $authUser->contact_id)->delete();
            }
        }
        $authUser->full_name = $request->full_name;
        $authUser->username = $request->username;
        $authUser->save();
        DB::commit();

        return response()->json([
            'message' => __('app_translation.profile_changed'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
