<?php

namespace app\components\daraja\contracts;

interface DarajaEndpointInterface
{
    public function request($endpointKey, array $data = [], array $options = []);

    public function getEndpoint($endpointKey);

    public function hasEndpoint($endpointKey);

    public function getEndpoints();
}
