<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\daraja\contracts\DarajaServiceInterface;
use app\models\DarajaRequestForm;
use Safaricom\Daraja\EndpointCatalog;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DarajaController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly DarajaServiceInterface $daraja,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        return $this->render('index', [
            'groups' => $this->serviceGroups(),
        ]);
    }

    public function actionRequest(string $endpoint = EndpointCatalog::STK_PUSH): string
    {
        $endpoints = $this->endpointOptions();
        if (!isset($endpoints[$endpoint])) {
            throw new NotFoundHttpException('Unknown Daraja endpoint.');
        }

        $model = new DarajaRequestForm();
        $model->endpointKey = $endpoint;
        $model->payload = $this->samplePayloads()[$endpoint] ?? [];
        $model->headers = $this->sampleHeaders($endpoint);
        $model->query = $this->sampleQuery($endpoint);

        $result = null;
        if ($model->load(Yii::$app->request->post())) {
            $result = $model->send($this->daraja);
        }

        return $this->render('request', [
            'model' => $model,
            'result' => $result,
            'endpoint' => $this->daraja->getEndpoint($endpoint),
            'endpointOptions' => $endpoints,
            'fieldHints' => $this->fieldHints(),
            'fullEndpointUrl' => $this->fullEndpointUrl($endpoint),
            'groups' => $this->serviceGroups(),
            'serviceNotice' => $this->serviceNotice($endpoint),
        ]);
    }

    private function serviceGroups(): array
    {
        return [
            'stk' => [
                'label' => 'STK Push',
                'endpoints' => [
                    EndpointCatalog::STK_PUSH => 'Process request',
                    EndpointCatalog::STK_QUERY => 'Query payment',
                ],
            ],
            'c2b' => [
                'label' => 'C2B',
                'endpoints' => [
                    EndpointCatalog::C2B_REGISTER_URL => 'Register URLs',
                    EndpointCatalog::C2B_SIMULATE => 'Simulate payment',
                ],
            ],
            'business-payments' => [
                'label' => 'Business Payments',
                'endpoints' => [
                    EndpointCatalog::B2C_PAYMENT => 'B2C payment',
                    EndpointCatalog::B2B_PAYMENT => 'B2B payment',
                    EndpointCatalog::B2POCHI_PAYMENT => 'B2Pochi payment',
                ],
            ],
            'transactions' => [
                'label' => 'Transactions',
                'endpoints' => [
                    EndpointCatalog::REVERSAL => 'Reversal',
                    EndpointCatalog::TRANSACTION_STATUS => 'Transaction status',
                    EndpointCatalog::ACCOUNT_BALANCE => 'Account balance',
                ],
            ],
            'ratiba' => [
                'label' => 'Ratiba',
                'endpoints' => [
                    EndpointCatalog::RATIBA_CREATE_PAYBILL => 'Paybill standing order',
                    EndpointCatalog::RATIBA_CREATE_BUY_GOODS => 'Buy Goods standing order',
                ],
            ],
            'lipa-na-bonga' => [
                'label' => 'Lipa na Bonga',
                'endpoints' => [
                    EndpointCatalog::LIPA_NA_BONGA_REDEEM_PAYBILL => 'Redeem Paybill',
                    EndpointCatalog::LIPA_NA_BONGA_CALCULATE_POINTS => 'Calculate points',
                ],
            ],
            'subscriber-info' => [
                'label' => 'Subscriber Info',
                'endpoints' => [
                    EndpointCatalog::IMSI_CHECK_ATI => 'IMSI CheckATI',
                    EndpointCatalog::SWAP_CHECK_ATI => 'SWAP CheckATI',
                ],
            ],
            'pull-transactions' => [
                'label' => 'Pull Transactions',
                'endpoints' => [
                    EndpointCatalog::PULL_REGISTER => 'Register callback',
                    EndpointCatalog::PULL_QUERY => 'Query transactions',
                ],
            ],
            'iot' => [
                'label' => 'IoT SIM Portal',
                'endpoints' => [
                    EndpointCatalog::IOT_SEARCH_MESSAGES => 'Search messages',
                    EndpointCatalog::IOT_FILTER_MESSAGES => 'Filter messages',
                    EndpointCatalog::IOT_DELETE_MESSAGE_THREAD => 'Delete message thread',
                    EndpointCatalog::IOT_GET_ALL_MESSAGES => 'Get all messages',
                    EndpointCatalog::IOT_SEND_SINGLE_MESSAGE => 'Send single message',
                    EndpointCatalog::IOT_DELETE_MESSAGE => 'Delete message',
                    EndpointCatalog::IOT_ALL_SIMS => 'All SIMs',
                    EndpointCatalog::IOT_QUERY_LIFECYCLE_STATUS => 'Lifecycle status',
                    EndpointCatalog::IOT_QUERY_CUSTOMER_INFO => 'Customer info',
                    EndpointCatalog::IOT_SIM_ACTIVATION => 'SIM activation',
                    EndpointCatalog::IOT_GET_ACTIVATION_TRENDS => 'Activation trends',
                    EndpointCatalog::IOT_RENAME_ASSET => 'Rename asset',
                    EndpointCatalog::IOT_GET_LOCATION_INFO => 'Location info',
                    EndpointCatalog::IOT_SUSPEND_UNSUSPEND_SUB => 'Suspend / unsuspend subscriber',
                ],
            ],
        ];
    }

    private function endpointOptions(): array
    {
        $options = [];
        foreach ($this->serviceGroups() as $group) {
            foreach ($group['endpoints'] as $key => $label) {
                $options[$key] = $group['label'] . ' - ' . $label;
            }
        }

        return $options;
    }

    private function sampleHeaders(string $endpoint): array
    {
        if (!str_starts_with($endpoint, 'iot.')) {
            return [];
        }

        return [
            'x-correlation-conversationid' => uniqid('', true),
            'x-source-system' => 'web-portal',
            'x-api-key' => getenv('DARAJA_IOT_API_KEY') ?: '',
            'Accept-Language' => 'EN',
            'X-MSISDN' => getenv('DARAJA_IOT_MSISDN') ?: '',
            'X-App' => 'web-portal',
            'X-MessageID' => uniqid('msg-', true),
        ];
    }

    private function sampleQuery(string $endpoint): array
    {
        $metadata = $this->daraja->getEndpoint($endpoint);
        return $metadata['query'] ?? [];
    }

    private function samplePayloads(): array
    {
        $shortCode = getenv('DARAJA_SHORT_CODE') ?: getenv('DARAJA_SHORTCODE') ?: '174379';
        $passkey = getenv('DARAJA_PASSKEY') ?: '';
        $phone = getenv('DARAJA_TEST_PHONE') ?: '254700000000';
        $initiator = getenv('DARAJA_INITIATOR_NAME') ?: 'testapi';
        $credential = getenv('DARAJA_SECURITY_CREDENTIAL') ?: '';
        $timestamp = date('YmdHis');
        $stkPassword = $passkey === '' ? '' : $this->daraja->generateStkPassword($shortCode, $passkey, $timestamp);

        return [
            EndpointCatalog::STK_PUSH => [
                'BusinessShortCode' => $shortCode,
                'Password' => $stkPassword,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => 1,
                'PartyA' => $phone,
                'PartyB' => $shortCode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $this->daraja->buildCallbackUrl('/daraja/stk-callback'),
                'AccountReference' => 'INV-1001',
                'TransactionDesc' => 'Invoice payment',
            ],
            EndpointCatalog::STK_QUERY => [
                'BusinessShortCode' => $shortCode,
                'Password' => $stkPassword,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => 'ws_CO_...',
            ],
            EndpointCatalog::C2B_REGISTER_URL => [
                'ShortCode' => $shortCode,
                'ResponseType' => 'Completed',
                'ConfirmationURL' => $this->daraja->buildCallbackUrl('/daraja/c2b-confirmation'),
                'ValidationURL' => $this->daraja->buildCallbackUrl('/daraja/c2b-validation'),
            ],
            EndpointCatalog::C2B_SIMULATE => [
                'ShortCode' => $shortCode,
                'CommandID' => 'CustomerPayBillOnline',
                'Amount' => '10',
                'Msisdn' => $phone,
                'BillRefNumber' => 'INV-1001',
            ],
            EndpointCatalog::B2C_PAYMENT => [
                'InitiatorName' => $initiator,
                'SecurityCredential' => $credential,
                'CommandID' => 'BusinessPayment',
                'Amount' => '100',
                'PartyA' => $shortCode,
                'PartyB' => $phone,
                'Remarks' => 'Payout',
                'QueueTimeOutURL' => $this->daraja->buildCallbackUrl('/daraja/timeout'),
                'ResultURL' => $this->daraja->buildCallbackUrl('/daraja/result'),
                'Occasion' => 'Refund',
            ],
            EndpointCatalog::B2B_PAYMENT => [
                'Initiator' => $initiator,
                'SecurityCredential' => $credential,
                'CommandID' => 'BusinessPayBill',
                'SenderIdentifierType' => '4',
                'RecieverIdentifierType' => '4',
                'Amount' => '100',
                'PartyA' => $shortCode,
                'PartyB' => '600000',
                'AccountReference' => 'INV-1001',
                'Remarks' => 'Supplier payment',
                'QueueTimeOutURL' => $this->daraja->buildCallbackUrl('/daraja/timeout'),
                'ResultURL' => $this->daraja->buildCallbackUrl('/daraja/result'),
            ],
            EndpointCatalog::B2POCHI_PAYMENT => [
                'OriginatorConversationID' => uniqid('b2pochi-', true),
                'InitiatorName' => $initiator,
                'SecurityCredential' => $credential,
                'CommandID' => 'BusinessPayment',
                'Amount' => '100',
                'PartyA' => $shortCode,
                'PartyB' => $phone,
                'Remarks' => 'B2Pochi payment',
                'QueueTimeOutURL' => $this->daraja->buildCallbackUrl('/daraja/timeout'),
                'ResultURL' => $this->daraja->buildCallbackUrl('/daraja/result'),
                'Occasion' => 'Payment',
            ],
            EndpointCatalog::REVERSAL => [
                'Initiator' => $initiator,
                'SecurityCredential' => $credential,
                'CommandID' => 'TransactionReversal',
                'TransactionID' => 'ABC123XYZ',
                'Amount' => '100',
                'ReceiverParty' => $shortCode,
                'RecieverIdentifierType' => '4',
                'ResultURL' => $this->daraja->buildCallbackUrl('/daraja/result'),
                'QueueTimeOutURL' => $this->daraja->buildCallbackUrl('/daraja/timeout'),
                'Remarks' => 'Customer refund',
                'Occasion' => 'Refund',
            ],
            EndpointCatalog::TRANSACTION_STATUS => [
                'Initiator' => $initiator,
                'SecurityCredential' => $credential,
                'CommandID' => 'TransactionStatusQuery',
                'TransactionID' => 'ABC123XYZ',
                'PartyA' => $shortCode,
                'IdentifierType' => '4',
                'ResultURL' => $this->daraja->buildCallbackUrl('/daraja/result'),
                'QueueTimeOutURL' => $this->daraja->buildCallbackUrl('/daraja/timeout'),
                'Remarks' => 'Status query',
                'Occasion' => 'Status',
            ],
            EndpointCatalog::ACCOUNT_BALANCE => [
                'Initiator' => $initiator,
                'SecurityCredential' => $credential,
                'CommandID' => 'AccountBalance',
                'PartyA' => $shortCode,
                'IdentifierType' => '4',
                'Remarks' => 'Balance query',
                'QueueTimeOutURL' => $this->daraja->buildCallbackUrl('/daraja/timeout'),
                'ResultURL' => $this->daraja->buildCallbackUrl('/daraja/result'),
            ],
            EndpointCatalog::RATIBA_CREATE_PAYBILL => [
                'StandingOrderName' => 'Monthly fee',
                'BusinessShortCode' => $shortCode,
                'TransactionType' => 'Standing Order Customer Pay Bill',
                'Amount' => '100',
                'PartyA' => $phone,
                'ReceiverPartyIdentifierType' => '4',
                'CallBackURL' => $this->daraja->buildCallbackUrl('/daraja/ratiba-callback'),
                'AccountReference' => 'ACC-1001',
                'TransactionDesc' => 'Monthly payment',
                'Frequency' => '1',
                'StartDate' => date('Ymd'),
                'EndDate' => date('Ymd', strtotime('+1 year')),
            ],
            EndpointCatalog::RATIBA_CREATE_BUY_GOODS => [
                'StandingOrderName' => 'Merchant subscription',
                'BusinessShortCode' => $shortCode,
                'TransactionType' => 'Standing Order Customer Pay Merchant',
                'Amount' => '100',
                'PartyA' => $phone,
                'ReceiverPartyIdentifierType' => '2',
                'CallBackURL' => $this->daraja->buildCallbackUrl('/daraja/ratiba-callback'),
                'AccountReference' => 'ACC-1001',
                'TransactionDesc' => 'Merchant payment',
                'Frequency' => '1',
                'StartDate' => date('Ymd'),
                'EndDate' => date('Ymd', strtotime('+1 year')),
            ],
            EndpointCatalog::LIPA_NA_BONGA_REDEEM_PAYBILL => [
                'msisdn' => $phone,
                'amount' => 100,
                'bongaPoints' => 500,
                'conversionRate' => 0.2,
                'shortCode' => $shortCode,
                'accountNumber' => 'ACC-1001',
            ],
            EndpointCatalog::LIPA_NA_BONGA_CALCULATE_POINTS => [
                'points' => '500',
            ],
            EndpointCatalog::IMSI_CHECK_ATI => [
                'customerNumber' => $phone,
            ],
            EndpointCatalog::SWAP_CHECK_ATI => [
                'customerNumber' => $phone,
            ],
            EndpointCatalog::PULL_REGISTER => [
                'ShortCode' => $shortCode,
                'RequestType' => 'Pull',
                'NominatedNumber' => $phone,
                'CallBackURL' => $this->daraja->buildCallbackUrl('/daraja/pull-callback'),
            ],
            EndpointCatalog::PULL_QUERY => [
                'ShortCode' => $shortCode,
                'StartDate' => date('Y-m-01 00:00:00'),
                'EndDate' => date('Y-m-d 23:59:59'),
                'OffSetValue' => '0',
            ],
            EndpointCatalog::IOT_SEARCH_MESSAGES => [
                'searchValue' => 'hello',
                'vpnGroup' => 'MY-GROUP',
                'username' => 'admin',
            ],
            EndpointCatalog::IOT_FILTER_MESSAGES => [
                'vpnGroup' => 'MY-GROUP',
                'username' => 'admin',
            ],
            EndpointCatalog::IOT_DELETE_MESSAGE_THREAD => [
                'threadId' => 'THREAD-ID',
                'vpnGroup' => 'MY-GROUP',
                'username' => 'admin',
            ],
            EndpointCatalog::IOT_GET_ALL_MESSAGES => [
                'vpnGroup' => 'MY-GROUP',
            ],
            EndpointCatalog::IOT_SEND_SINGLE_MESSAGE => [
                'msisdn' => $phone,
                'message' => 'Test message',
                'vpnGroup' => 'MY-GROUP',
                'username' => 'admin',
            ],
            EndpointCatalog::IOT_DELETE_MESSAGE => [
                'messageId' => 'MESSAGE-ID',
                'vpnGroup' => 'MY-GROUP',
                'username' => 'admin',
            ],
            EndpointCatalog::IOT_ALL_SIMS => [
                'vpnGroup' => 'MY-GROUP',
            ],
            EndpointCatalog::IOT_QUERY_LIFECYCLE_STATUS => [
                'msisdn' => $phone,
                'vpnGroup' => 'MY-GROUP',
            ],
            EndpointCatalog::IOT_QUERY_CUSTOMER_INFO => [
                'msisdn' => $phone,
                'vpnGroup' => 'MY-GROUP',
            ],
            EndpointCatalog::IOT_SIM_ACTIVATION => [
                'msisdn' => $phone,
                'vpnGroup' => 'MY-GROUP',
                'username' => 'admin',
            ],
            EndpointCatalog::IOT_GET_ACTIVATION_TRENDS => [
                'vpnGroup' => 'MY-GROUP',
            ],
            EndpointCatalog::IOT_RENAME_ASSET => [
                'msisdn' => $phone,
                'assetName' => 'Vehicle 001',
                'vpnGroup' => 'MY-GROUP',
            ],
            EndpointCatalog::IOT_GET_LOCATION_INFO => [
                'msisdn' => $phone,
                'vpnGroup' => 'MY-GROUP',
            ],
            EndpointCatalog::IOT_SUSPEND_UNSUSPEND_SUB => [
                'msisdn' => $phone,
                'action' => 'SUSPEND',
                'vpnGroup' => 'MY-GROUP',
                'username' => 'admin',
            ],
        ];
    }

    private function fieldHints(): array
    {
        return [
            'BusinessShortCode' => 'Your Paybill or Till number.',
            'Password' => 'STK password generated from short code, passkey, and timestamp.',
            'Timestamp' => 'Format: YYYYMMDDHHMMSS.',
            'TransactionType' => 'Example: CustomerPayBillOnline.',
            'PhoneNumber' => 'Customer phone in 2547XXXXXXXX format.',
            'PartyA' => 'Sender phone number or organization short code.',
            'PartyB' => 'Receiver phone number, Till, Paybill, or organization short code.',
            'CallBackURL' => 'Public HTTPS URL Safaricom can call.',
            'ResultURL' => 'Public HTTPS URL for asynchronous result callbacks.',
            'QueueTimeOutURL' => 'Public HTTPS URL for timeout callbacks.',
            'SecurityCredential' => 'Encrypted initiator password.',
            'Initiator' => 'Safaricom initiator name.',
            'InitiatorName' => 'Safaricom initiator name.',
            'Amount' => 'Transaction amount.',
            'CheckoutRequestID' => 'Returned from the original STK push request.',
            'TransactionID' => 'M-Pesa transaction ID.',
            'ShortCode' => 'Paybill, Till, or organization short code.',
            'Msisdn' => 'Customer phone in 2547XXXXXXXX format.',
            'x-api-key' => 'IoT SIM Portal API key.',
            'X-MSISDN' => 'IoT portal MSISDN header.',
            'pageNo' => 'Page number.',
            'pageSize' => 'Number of records per page.',
        ];
    }

    private function serviceNotice(string $endpoint): ?string
    {
        if (str_starts_with($endpoint, 'lipa_na_bonga.')) {
            return 'Safaricom may return HTTP 404 for Lipa na Bonga if this API is not enabled for your Daraja app '
                . 'or is not available in the selected sandbox/production environment.';
        }

        return null;
    }

    private function fullEndpointUrl(string $endpointKey): string
    {
        $endpoint = $this->daraja->getEndpoint($endpointKey);
        if ($endpoint === null) {
            return '';
        }

        $override = getenv('DARAJA_ENDPOINT_' . strtoupper(str_replace(['.', '-'], '_', $endpointKey)));
        $path = $override ?: $endpoint['path'];
        $baseUrl = Yii::$app->daraja->getBaseUrl();

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
