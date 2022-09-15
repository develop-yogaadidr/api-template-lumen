<?php

namespace App\Enums;

abstract class MaxLength implements IEnumsClass
{
    const Notes = 300;
    const Address = 250;
    const Password = 250;
    const Status = 100;
    const Title = 100;
    const Email = 100;
    const Name = 50;
    const Phone = 15;

    public function getEnumsArray()
    {
        return ['Notes', 'Address', 'Password', 'Status','Title', 'Email', 'Name', 'Phone'];
    }
}
