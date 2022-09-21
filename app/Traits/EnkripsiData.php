<?php

namespace App\Traits;
use Illuminate\Support\Facades\Crypt;

trait EnkripsiData
{
    public function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }

    public function decryptData($data)
    {
        $data = Crypt::decrypt($data);
        return $data;
    }
}