<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Enums\StatusCodes;
use App\Helpers\StringHelper;
use Kreait\Firebase\Messaging;

class UserController extends CrudController 
{
    public function __construct(Messaging $messaging)
    {
        $this->middleware('auth:api', ['except' => ['resetPassword', 'validateResetPassword', 'changePassword']]);

        $model = new User;
        parent::__construct($model);
        $this->messaging = $messaging;
        $this->createRules = [
            'name' => 'required|max:50',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|required_with:retype_password|same:retype_password',
            // 'password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|required_with:retype_password|same:retype_password',
            'retype_password' => 'required|min:6|required_with:password|same:password',
            // 'retype_password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|required_with:password|same:password',
        ];
        $this->updateRules = [
            'name' => 'required|max:50',
            'email' => 'required|max:100',
            'password' => 'prohibited',
        ];
    }

    public function create(Request $request)
    {
        if (!$this->ensureEmailAvailable($request->email)) {
            return response()->json(array("message" => "Email sudah terdaftar"), StatusCodes::UnprocessableEntity);
        }

        $this->validate($request, $this->createRules);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->verified_at = $this->getDbTimeNow();
        $user->save();

        // TODO: send email verification
        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6|required_with:retype_password|same:retype_password',
            'retype_password' => 'required|min:6|required_with:new_password|same:new_password',
        ]);

        $user = User::where('id', auth()->user()->id)->firstOrFail();
        if (!Hash::check($request->old_password, $user->password)) {
            abort(StatusCodes::UnprocessableEntity, 'Old password didn`t match');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(null, StatusCodes::NoContent);
    }

    //// Firebase section
    public function updateFcmToken(Request $request)
    {
        $this->validate($request, [
            'fcm_token' => 'required',
        ]);

        $user = User::where('id', auth()->user()->id)->firstOrFail();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(null, 204);
    }
    
    public function revokeFcmToken(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->firstOrFail();
        $user->fcm_token = null;
        $user->save();

        return response()->json(null, StatusCodes::NoContent);
    }

    //// Forgot password section
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $stringHelper = new StringHelper();
        $token = $stringHelper->generateUniqueToken();

        // make all requests by email not valid anymore
        ForgotPassword::where('email', $request->email)->where('is_valid', 1)->update(array('is_valid' => 0));

        $forgotPassword = new ForgotPassword();
        $forgotPassword->user_id = $user->id;
        $forgotPassword->email = $user->email;
        $forgotPassword->token = Hash::make($token);
        $forgotPassword->expired_at = $this->getDbTimeNow(1);
        $forgotPassword->save();

        $maildata = [
            'title' => 'Halo, ' . $user->name,
            'token' =>  $token,
            'body' => "Berikut kode verifikasi untuk melanjutkan proses reset password:"
        ];
        $this->sendEmail($user->email, "Reset Password", $maildata);

        return response()->json(array('message' => "Permintaan reset password terkirim"), StatusCodes::Ok);
    }

    public function validateResetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'token' => 'required',
        ]);

        $forgotPassword = ForgotPassword::where('email', $request->email)
            ->where('is_used', 0)
            ->where('is_valid', 1)
            ->where('is_verified', 0)
            ->where('expired_at', '>', $this->getDbTimeNow())
            ->firstOrFail();

        $valid = false;
        $forgotPasswordId = 0;
        $token = "";
        if (Hash::check($request->token, $forgotPassword->token)) {
            $forgotPasswordId = $forgotPassword->id;
            $token = $forgotPassword->token;
            $valid = true;
        }

        if ($valid) {
            $response = ForgotPassword::findOrFail($forgotPasswordId);
            $response->is_verified = 1;
            $response->save();

            return response()->json(array('token' => $token), 200);
        }

        abort(StatusCodes::BadRequest, 'Token not found.');
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'new_password' => 'required|min:6|required_with:retype_password|same:retype_password',
            'retype_password' => 'required|min:6|required_with:new_password|same:new_password',
        ]);

        $forgotPassword = ForgotPassword::where('token', $request->token)
            ->where('is_used', 0)
            ->where('is_valid', 1)
            ->where('is_verified', 1)
            ->where('expired_at', '>', $this->getDbTimeNow())
            ->firstOrFail();

        $response = User::findOrFail($forgotPassword->user_id);
        $response->password = Hash::make($request->new_password);
        $response->save();

        $update = ForgotPassword::findOrFail($forgotPassword->id);
        $update->is_used = 1;
        $update->is_valid = 0;
        $update->save();

        return response()->json(null, 204);
    }

    private function ensureEmailAvailable($email)
    {
        $user = User::where('email', $email)->first();
        return $user == null;
    }
}
