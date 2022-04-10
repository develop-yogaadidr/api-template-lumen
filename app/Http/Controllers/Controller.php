<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function requestToArray($request)
    {
        $arrResponse = array();
        foreach($request->all() as $key => $value)
        {
            $arrResponse[$key] = $value;
        }

        return $arrResponse;
    }

    protected function validateRequestInput($request, $fillable)
    {
        $isSuccess = false;
        foreach($request->all() as $key => $value)
        {
            foreach($fillable as $allowed)
            {
                if($key === $allowed) {
                    $isSuccess = true;
                }
            }
        }

        if(!$isSuccess) abort(400, 'Missing some properties');
    }
}
