<?php

declare(strict_types=1);

namespace app\models;

use app\components\daraja\contracts\DarajaServiceInterface;
use Safaricom\Daraja\DarajaException;
use yii\base\Model;

class DarajaRequestForm extends Model
{
    public string $endpointKey = '';
    public array $payload = [];
    public array $headers = [];
    public array $query = [];

    public function rules(): array
    {
        return [
            [['endpointKey'], 'required'],
            [['endpointKey'], 'string', 'max' => 100],
            [['payload', 'headers', 'query'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'endpointKey' => 'Endpoint',
        ];
    }

    public function send(DarajaServiceInterface $daraja): array
    {
        if (!$this->validate()) {
            return [
                'ok' => false,
                'errors' => $this->getErrors(),
            ];
        }

        $payload = $this->normalize($this->payload);
        if (in_array($this->endpointKey, ['stk.push', 'stk.query'], true)) {
            $shortCode = $payload['BusinessShortCode'] ?? getenv('DARAJA_SHORT_CODE') ?: getenv('DARAJA_SHORTCODE') ?: '';
            $timestamp = $payload['Timestamp'] ?? date('YmdHis');
            $passkey = getenv('DARAJA_PASSKEY') ?: '';

            $payload['BusinessShortCode'] = $shortCode;
            $payload['Timestamp'] = $timestamp;
            if ($passkey !== '') {
                $payload['Password'] = $daraja->generateStkPassword($shortCode, $passkey, $timestamp);
            }
        }

        if ($this->endpointKey === 'stk.push') {
            $payload['PartyA'] = $payload['PhoneNumber'] ?? '';
            $payload['PartyB'] = $payload['BusinessShortCode'] ?? '';
        }

        try {
            return [
                'ok' => true,
                'data' => $daraja->request(
                    $this->endpointKey,
                    $payload,
                    [
                        'headers' => $this->normalize($this->headers),
                        'query' => $this->normalize($this->query),
                    ],
                ),
            ];
        } catch (DarajaException $exception) {
            return [
                'ok' => false,
                'exception' => [
                    'message' => $exception->getMessage(),
                    'statusCode' => $exception->getStatusCode(),
                    'endpointKey' => $exception->getEndpointKey(),
                    'responseData' => $exception->getResponseData(),
                ],
            ];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'exception' => [
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    private function normalize(array $values): array
    {
        $normalized = [];
        foreach ($values as $key => $value) {
            if ($value === '') {
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }
}
