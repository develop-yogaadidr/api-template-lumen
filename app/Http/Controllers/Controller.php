<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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

    protected function getDbTimeNow($addHour = 0)
    {
        $result = DB::table('users')
        ->select(DB::raw('DATE_ADD(NOW(), INTERVAL '.$addHour.' HOUR) as date_time'))
        ->get();

        return $result[0]->date_time;
    }
}
