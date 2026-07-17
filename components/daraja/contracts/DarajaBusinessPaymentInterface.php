<?php

namespace app\components\daraja\contracts;

interface DarajaBusinessPaymentInterface
{
    public function b2cPayment(array $data);

    public function b2bPayment(array $data);

    public function b2PochiPayment(array $data);
}
