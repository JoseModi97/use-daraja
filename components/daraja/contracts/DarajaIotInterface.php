<?php

namespace app\components\daraja\contracts;

interface DarajaIotInterface
{
    public function iot($endpointKey, array $data = [], array $headers = [], array $query = []);

    public function iotSearchMessages(array $data, array $headers = [], array $query = []);

    public function iotFilterMessages(array $data, array $headers = [], array $query = []);

    public function iotDeleteMessageThread(array $data, array $headers = []);

    public function iotGetAllMessages(array $data, array $headers = [], array $query = []);

    public function iotSendSingleMessage(array $data, array $headers = []);

    public function iotDeleteMessage(array $data, array $headers = []);

    public function iotAllSims(array $data, array $headers = [], array $query = []);

    public function iotQueryLifecycleStatus(array $data, array $headers = []);

    public function iotQueryCustomerInfo(array $data, array $headers = []);

    public function iotSimActivation(array $data, array $headers = []);

    public function iotGetActivationTrends(array $data, array $headers = []);

    public function iotRenameAsset(array $data, array $headers = []);

    public function iotGetLocationInfo(array $data, array $headers = []);

    public function iotSuspendUnsuspendSub(array $data, array $headers = []);
}
