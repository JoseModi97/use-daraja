<?php

namespace app\components\daraja\contracts;

interface DarajaUtilityInterface
{
    public function generateStkPassword($businessShortCode, $passkey, $timestamp = null);

    public function generateSecurityCredential($initiatorPassword, $certificatePath);
}
