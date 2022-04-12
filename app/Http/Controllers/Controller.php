<?php

namespace App\Http\Controllers;

use app\Helpers\MessageParameter;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Messaging\CloudMessage;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public $messaging;

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

    /**
     * Send Firebase Cloud Messaging
     * 
     * @param MessageParameter $params
     */
    protected function sendMessage(MessageParameter $params)
    {
        $message = CloudMessage::withTarget($params->getTarget()->getKey(), $params->getTarget()->getValue())
            ->withNotification($params->getNotification());
        if($params->getData() != null) $message->withData($params->getData());
        
        $this->messaging->send($message);
    }
}
