<?php

namespace app\components\daraja\contracts;

interface DarajaSubscriberInfoInterface
{
    public function imsiCheckAti(array $data);

    public function swapCheckAti(array $data);
}
