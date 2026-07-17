<?php

namespace app\components\daraja;

use app\components\daraja\contracts\DarajaServiceInterface;
use Safaricom\Daraja\Daraja;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class DarajaService extends Component implements DarajaServiceInterface
{
    /**
     * @var Daraja|string|array|null
     */
    public $client;

    public function init()
    {
        parent::init();

        if ($this->client === null) {
            $this->client = Yii::$app->daraja;
            return;
        }

        if (is_string($this->client) && Yii::$app->has($this->client)) {
            $this->client = Yii::$app->get($this->client);
            return;
        }

        if (is_array($this->client) || is_string($this->client)) {
            $this->client = Yii::createObject($this->client);
        }
    }

    public function generateAccessToken()
    {
        return $this->getClient()->generateAccessToken();
    }

    public function getAccessToken()
    {
        return $this->getClient()->getAccessToken();
    }

    public function setAccessToken($accessToken, $expiresIn = null)
    {
        $this->getClient()->setAccessToken($accessToken, $expiresIn);
        return $this;
    }

    public function getTokenExpiresAt()
    {
        return $this->getClient()->getTokenExpiresAt();
    }

    public function request($endpointKey, array $data = [], array $options = [])
    {
        $endpoint = $this->getEndpointOverride($endpointKey);
        if ($endpoint !== null) {
            return $this->sendOverrideRequest($endpointKey, $endpoint, $data, $options);
        }

        return $this->getClient()->request($endpointKey, $data, $options);
    }

    public function getEndpoint($endpointKey)
    {
        return $this->getClient()->getEndpoint($endpointKey);
    }

    public function hasEndpoint($endpointKey)
    {
        return $this->getClient()->hasEndpoint($endpointKey);
    }

    public function getEndpoints()
    {
        return $this->getClient()->getEndpoints();
    }

    public function stkPush(array $data)
    {
        return $this->getClient()->stkPush($data);
    }

    public function stkQuery(array $data)
    {
        return $this->getClient()->stkQuery($data);
    }

    public function c2bRegisterUrl(array $data)
    {
        return $this->getClient()->c2bRegisterUrl($data);
    }

    public function c2bSimulate(array $data)
    {
        return $this->getClient()->c2bSimulate($data);
    }

    public function b2cPayment(array $data)
    {
        return $this->getClient()->b2cPayment($data);
    }

    public function b2bPayment(array $data)
    {
        return $this->getClient()->b2bPayment($data);
    }

    public function b2PochiPayment(array $data)
    {
        return $this->getClient()->b2PochiPayment($data);
    }

    public function reversal(array $data)
    {
        return $this->getClient()->reversal($data);
    }

    public function transactionStatus(array $data)
    {
        return $this->getClient()->transactionStatus($data);
    }

    public function accountBalance(array $data)
    {
        return $this->getClient()->accountBalance($data);
    }

    public function ratibaCreatePaybill(array $data)
    {
        return $this->getClient()->ratibaCreatePaybill($data);
    }

    public function ratibaCreateBuyGoods(array $data)
    {
        return $this->getClient()->ratibaCreateBuyGoods($data);
    }

    public function lipaNaBongaRedeemPaybill(array $data)
    {
        return $this->getClient()->lipaNaBongaRedeemPaybill($data);
    }

    public function lipaNaBongaCalculatePoints(array $data)
    {
        return $this->getClient()->lipaNaBongaCalculatePoints($data);
    }

    public function imsiCheckAti(array $data)
    {
        return $this->getClient()->imsiCheckAti($data);
    }

    public function swapCheckAti(array $data)
    {
        return $this->getClient()->swapCheckAti($data);
    }

    public function pullRegister(array $data)
    {
        return $this->getClient()->pullRegister($data);
    }

    public function pullQuery(array $data)
    {
        return $this->getClient()->pullQuery($data);
    }

    public function iot($endpointKey, array $data = [], array $headers = [], array $query = [])
    {
        return $this->getClient()->iot($endpointKey, $data, $headers, $query);
    }

    public function iotSearchMessages(array $data, array $headers = [], array $query = [])
    {
        return $this->getClient()->iotSearchMessages($data, $headers, $query);
    }

    public function iotFilterMessages(array $data, array $headers = [], array $query = [])
    {
        return $this->getClient()->iotFilterMessages($data, $headers, $query);
    }

    public function iotDeleteMessageThread(array $data, array $headers = [])
    {
        return $this->getClient()->iotDeleteMessageThread($data, $headers);
    }

    public function iotGetAllMessages(array $data, array $headers = [], array $query = [])
    {
        return $this->getClient()->iotGetAllMessages($data, $headers, $query);
    }

    public function iotSendSingleMessage(array $data, array $headers = [])
    {
        return $this->getClient()->iotSendSingleMessage($data, $headers);
    }

    public function iotDeleteMessage(array $data, array $headers = [])
    {
        return $this->getClient()->iotDeleteMessage($data, $headers);
    }

    public function iotAllSims(array $data, array $headers = [], array $query = [])
    {
        return $this->getClient()->iotAllSims($data, $headers, $query);
    }

    public function iotQueryLifecycleStatus(array $data, array $headers = [])
    {
        return $this->getClient()->iotQueryLifecycleStatus($data, $headers);
    }

    public function iotQueryCustomerInfo(array $data, array $headers = [])
    {
        return $this->getClient()->iotQueryCustomerInfo($data, $headers);
    }

    public function iotSimActivation(array $data, array $headers = [])
    {
        return $this->getClient()->iotSimActivation($data, $headers);
    }

    public function iotGetActivationTrends(array $data, array $headers = [])
    {
        return $this->getClient()->iotGetActivationTrends($data, $headers);
    }

    public function iotRenameAsset(array $data, array $headers = [])
    {
        return $this->getClient()->iotRenameAsset($data, $headers);
    }

    public function iotGetLocationInfo(array $data, array $headers = [])
    {
        return $this->getClient()->iotGetLocationInfo($data, $headers);
    }

    public function iotSuspendUnsuspendSub(array $data, array $headers = [])
    {
        return $this->getClient()->iotSuspendUnsuspendSub($data, $headers);
    }

    public function generateStkPassword($businessShortCode, $passkey, $timestamp = null)
    {
        return $this->getClient()->generateStkPassword($businessShortCode, $passkey, $timestamp);
    }

    public function generateSecurityCredential($initiatorPassword, $certificatePath)
    {
        return $this->getClient()->generateSecurityCredential($initiatorPassword, $certificatePath);
    }

    protected function getClient()
    {
        if (!$this->client instanceof Daraja) {
            throw new InvalidConfigException('DarajaService::$client must be an instance of Safaricom\\Daraja\\Daraja.');
        }

        return $this->client;
    }

    private function getEndpointOverride(string $endpointKey): ?array
    {
        $envName = 'DARAJA_ENDPOINT_' . strtoupper(str_replace(['.', '-'], '_', $endpointKey));
        $path = getenv($envName);
        if (!$path) {
            return null;
        }

        $endpoint = $this->getClient()->getEndpoint($endpointKey);
        if ($endpoint === null) {
            return null;
        }

        $endpoint['path'] = $path;
        return $endpoint;
    }

    private function sendOverrideRequest(string $endpointKey, array $endpoint, array $data, array $options)
    {
        $query = $endpoint['query'] ?? [];
        if (isset($options['query']) && is_array($options['query'])) {
            $query = ArrayHelper::merge($query, $options['query']);
        }

        $headers = ArrayHelper::merge(
            $this->getClient()->defaultHeaders,
            isset($options['headers']) && is_array($options['headers']) ? $options['headers'] : [],
        );
        if (!$this->hasHeader($headers, 'Authorization')) {
            $headers['Authorization'] = 'Bearer ' . $this->getClient()->getAccessToken();
        }

        $url = $endpoint['path'];
        if (!empty($query)) {
            $url = array_merge([$endpoint['path']], $query);
        }

        $request = $this->getClient()
            ->getHttpClient()
            ->createRequest()
            ->setMethod($endpoint['method'])
            ->setUrl($url)
            ->addHeaders($headers);

        if (strtoupper($endpoint['method']) !== 'GET') {
            $request->setFormat($options['format'] ?? $this->getClient()->requestFormat)
                ->setData($data);
        }

        try {
            $response = $request->send();
        } catch (\Exception $exception) {
            throw new \Safaricom\Daraja\DarajaException(
                'Safaricom API request could not be sent: ' . $exception->getMessage(),
                0,
                $exception,
                null,
                null,
                $endpointKey,
            );
        }

        $responseData = $response->getData();
        if (!$response->getIsOk()) {
            throw \Safaricom\Daraja\DarajaException::forHttpResponse(
                $response->statusCode,
                $responseData ?: $response->content,
                $endpointKey,
            );
        }

        return $responseData;
    }

    private function hasHeader(array $headers, string $name): bool
    {
        foreach ($headers as $headerName => $value) {
            if (strtolower((string) $headerName) === strtolower($name)) {
                return true;
            }
        }

        return false;
    }
}
