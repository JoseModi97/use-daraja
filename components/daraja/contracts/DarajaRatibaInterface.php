<?php

namespace app\components\daraja\contracts;

interface DarajaRatibaInterface
{
    public function ratibaCreatePaybill(array $data);

    public function ratibaCreateBuyGoods(array $data);
}
