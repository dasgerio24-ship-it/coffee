<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;

class NullSmsSender implements SmsSender
{
    public function send(string $phone, string $text): void {}
}
