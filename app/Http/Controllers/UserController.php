<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Enums\StatusCodes;
use App\Helpers\StringHelper;

class UserController extends CrudController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['resetPassword', 'validateResetPassword', 'changePassword']]);

        $model = new User;
        parent::__construct($model);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|unique:users',
            'password' => 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|confirmed',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'nullable|max:50',
            'email' => 'prohibited',
            'password' => 'prohibited',
        ]);

        return parent::update($request, $id);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
            'new_password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|required_with:retype_password|same:retype_password',
            'retype_password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|required_with:new_password|same:new_password',
        ]);

        $user = User::where('id', auth()->user()->id)->firstOrFail();
        if (!Hash::check($request->old_password, $user->password)) {
            abort(StatusCodes::UnprocessableEntity, 'Old password didn`t match');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(null, 204);
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

        //TODO: send email to user within data $token 
        return response()->json(array('verification_code' => $token), 200);
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
            'new_password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|required_with:retype_password|same:retype_password',
            'retype_password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|required_with:new_password|same:new_password',
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
}
