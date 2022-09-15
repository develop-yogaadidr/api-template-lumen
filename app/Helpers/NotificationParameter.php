<?php

namespace App\Helpers;

use Illuminate\Validation\ValidationException;

class NotificationParameter
{
    public $title;
    public $body;
    public $data;

    public $token;
    public $topic;

    public function getTarget()
    {
        if (isset($this->token) && isset($this->topic)) {
            $validationResponse = new ValidationResponse();
            $validationResponse->original = [
                "token" => [
                    "Please choose using token or topic"
                ],
                "topic" => [
                    "Please choose using token or topic"
                ]
            ];
            throw new ValidationException("Please choose using token or topic", $validationResponse);
        }

        if (!isset($this->token) && !isset($this->topic)) {
            $validationResponse = new ValidationResponse();
            $validationResponse->original = [
                "token" => [
                    "Please specify the token"
                ],
                "topic" => [
                    "Please specify the topic"
                ]
            ];
            throw new ValidationException("Please specify the target (token / topic)", $validationResponse);
        }

        return isset($this->token) ? $this->token : "/topics/" . $this->topic;
    }
}
