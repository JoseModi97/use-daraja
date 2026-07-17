<?php

namespace app\components\daraja\contracts;

interface DarajaAuthInterface
{
    public function generateAccessToken();

    public function getAccessToken();

    public function setAccessToken($accessToken, $expiresIn = null);

    public function getTokenExpiresAt();
}
