<?php

namespace app\components\daraja\contracts;

interface DarajaTransactionInterface
{
    public function reversal(array $data);

    public function transactionStatus(array $data);

    public function accountBalance(array $data);
}
