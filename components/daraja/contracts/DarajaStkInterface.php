<?php

namespace app\components\daraja\contracts;

interface DarajaStkInterface
{
    public function stkPush(array $data);

    public function stkQuery(array $data);
}
