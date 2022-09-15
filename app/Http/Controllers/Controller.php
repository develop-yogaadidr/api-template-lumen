<?php

namespace App\Http\Controllers;

use App\Helpers\MailParameter;
use app\Helpers\MessageParameter;
use App\Helpers\NotificationParameter;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Messaging\CloudMessage;
use Laravel\Lumen\Routing\Controller as BaseController;

define('API_ACCESS_KEY', env('API_ACCESS_KEY'));
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
        if ($params->getData() != null) $message->withData($params->getData());

        $this->messaging->send($message);
    }

    protected function uploadImage($context, $id, $request, $key)
    {
        $path = 'images/' . $context . '/' . $id;
        $image = $request->file($key);
        if ($image == null) {
            return null;
        }
        $filename = $key . '-' . str_replace(' ', '-', strtolower($image->getClientOriginalName()));
        $image->move($path, $filename);

        return $path . '/' . $filename;
    }

    protected function sendEmail(MailParameter $parameter)
    {
        Mail::to($parameter->id)->send(new SendEmail($parameter->maildata, $parameter->subject));
    }

    protected function sendNotification(NotificationParameter $parameter)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $parameter->title,
            'body' => $parameter->body
        ];
        $fcmNotification = [
            'to'        => $parameter->getTarget(),
            'notification' => $notification,
            'data' => $parameter->data,
            'priority' => 'high'
        ];

        $headers = [
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
