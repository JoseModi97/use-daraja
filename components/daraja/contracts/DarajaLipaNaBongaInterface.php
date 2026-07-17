<?php

namespace app\components\daraja\contracts;

interface DarajaLipaNaBongaInterface
{
    public function lipaNaBongaRedeemPaybill(array $data);

    public function lipaNaBongaCalculatePoints(array $data);
}
