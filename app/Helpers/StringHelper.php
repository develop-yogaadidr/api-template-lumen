<?php

namespace App\Helpers;

class StringHelper
{
    public function generateUniqueToken()
    {
        $length = 6;
        $token = "";

        for($i = 0; $i < $length; $i++){
            $token = $token.''.rand(1, 9);
        }

        return $token;
    }
}