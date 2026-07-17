<?php

namespace app\components\daraja\contracts;

interface DarajaPullTransactionsInterface
{
    public function pullRegister(array $data);

    public function pullQuery(array $data);
}
