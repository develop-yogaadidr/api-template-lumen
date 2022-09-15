<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    protected $fillable = ['name', 'email', 'verified_at', 'password', 'remember_token', 'photo'];

    protected $guarded = ['created_at', 'updated_at', 'id'];

    protected $hidden = ['password', 'remember_token', 'fcm_token', 'verified_at', 'created_at', 'updated_at'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Use this method to validate input. See CrudController.php
     *
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    public function forgotPassword()
    {
        return $this->hasMany(ForgotPassword::class, 'user_id', 'id');
    }
}
