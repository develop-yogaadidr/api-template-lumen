<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function requestToArray($request)
    {
        $arrResponse = array();
        foreach($request->all() as $key => $value){
            $arrResponse[$key] = $value;
        }

        return $arrResponse;
    }
}
