<?php

namespace App\Helpers;

use Kreait\Firebase\Messaging\Notification;

class MessageParameter
{
    protected $data = null;
    protected Notification $notification;
    protected MessageTargetData $target;

    public function getData()
    {
        return $this->data;
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function getTarget()
    {
        return $this->target;
    }

    /**
     * 
     * @param Array $data. Key value array
     * 
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 
     * @param string $title
     * @param string $body
     * 
     */
    public function setNotification($title, $body)
    {
        $this->notification = Notification::create($title, $body);
    }

    /**
     * 
     * @param TopicTargets $target. See App\Enums\TopicTargets.php
     * @param string $value
     * 
     */
    public function setTarget($key, $value)
    {
        $this->target = new MessageTargetData;
        $this->target->setKey($key);
        $this->target->setValue($value);
    }
}

class MessageTargetData
{
    protected $key;
    protected $value;

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}