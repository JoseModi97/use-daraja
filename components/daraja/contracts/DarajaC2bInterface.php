<?php

namespace app\components\daraja\contracts;

interface DarajaC2bInterface
{
    public function c2bRegisterUrl(array $data);

    public function c2bSimulate(array $data);
}
