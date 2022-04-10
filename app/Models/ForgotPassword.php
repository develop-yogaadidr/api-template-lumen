<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    protected $fillable = ['user_id', 'token', 'expired', 'expired_at'];

    protected $guarded = ['created_at', 'updated_at', 'id'];

    protected $hidden = ['token',  'created_at', 'updated_at'];

    /**
     * Use this method to validate input. See CrudController.php
     *
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable;
    }
}
