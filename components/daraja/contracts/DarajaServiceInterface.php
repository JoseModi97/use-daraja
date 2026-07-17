<?php

namespace app\components\daraja\contracts;

interface DarajaServiceInterface extends
    DarajaAuthInterface,
    DarajaEndpointInterface,
    DarajaStkInterface,
    DarajaC2bInterface,
    DarajaBusinessPaymentInterface,
    DarajaTransactionInterface,
    DarajaRatibaInterface,
    DarajaLipaNaBongaInterface,
    DarajaSubscriberInfoInterface,
    DarajaPullTransactionsInterface,
    DarajaIotInterface,
    DarajaUtilityInterface
{
}
