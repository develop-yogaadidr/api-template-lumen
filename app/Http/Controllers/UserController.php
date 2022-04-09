<?php

namespace App\Http\Controllers;
use App\Models\User;

class UserController extends CrudController
{
    public function __construct()
    {
        $model = new User;
        parent::__construct($model);
    }
}
