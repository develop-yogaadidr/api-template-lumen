<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends CrudController
{
    public function __construct()
    {
        $this->middleware('auth:api');

        $model = new User;
        parent::__construct($model);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        $request->all()['password'] = Hash::make($request->password);

        return parent::create($request);
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
}
