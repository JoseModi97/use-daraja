# Use Daraja

Yii2 web app for exercising Safaricom Daraja services from a field-based interface. It wraps the installed `josemodi97/yii2-safaricom-daraja` component with local service interfaces and user-facing request forms.

## Features

- Daraja service menu for STK Push, C2B, B2B/B2C/B2Pochi, transactions, Ratiba, Lipa na Bonga, subscriber checks, pull transactions, and IoT SIM Portal.
- Field-based request forms instead of raw JSON payload editing.
- STK Push password generation on submit from shortcode, passkey, and timestamp.
- Internal STK `PartyA` and `PartyB` handling: users only enter the phone number and shortcode.
- `.env`-driven credentials with `.env.example` committed and `.env` ignored.
- Optional endpoint override support for Daraja APIs whose sandbox path differs from the package catalog.

## Screenshots

Use the top navigation menu and choose **Daraja**. The services page lists every supported Daraja API group.

![Daraja services overview](docs/screenshots/daraja-services.png)

### STK Push

Open **Daraja -> STK Push**. The app hides generated/internal fields and generates the STK password before submitting.

![STK Push process request](docs/screenshots/stk-push-process-request.png)

![STK Push query payment](docs/screenshots/stk-push-query-payment.png)

### C2B

![C2B register URLs](docs/screenshots/c2b-register-urls.png)

![C2B simulate payment](docs/screenshots/c2b-simulate-payment.png)

### Business Payments

![B2C payment](docs/screenshots/business-b2c-payment.png)

![B2B payment](docs/screenshots/business-b2b-payment.png)

![B2Pochi payment](docs/screenshots/business-b2pochi-payment.png)

### Transactions

![Transaction reversal](docs/screenshots/transactions-reversal.png)

![Transaction status](docs/screenshots/transactions-status.png)

![Account balance](docs/screenshots/transactions-account-balance.png)

### Ratiba

![Ratiba Paybill standing order](docs/screenshots/ratiba-paybill-standing-order.png)

![Ratiba Buy Goods standing order](docs/screenshots/ratiba-buy-goods-standing-order.png)

### Lipa na Bonga

The page shows the exact Safaricom URL being called, which helps when checking sandbox endpoint availability.

![Lipa na Bonga redeem Paybill](docs/screenshots/lipa-na-bonga-redeem-paybill.png)

![Lipa na Bonga calculate points](docs/screenshots/lipa-na-bonga-calculate-points.png)

### Subscriber Info

![IMSI CheckATI](docs/screenshots/subscriber-imsi-checkati.png)

![SWAP CheckATI](docs/screenshots/subscriber-swap-checkati.png)

### Pull Transactions

![Pull Transactions register callback](docs/screenshots/pull-register-callback.png)

![Pull Transactions query](docs/screenshots/pull-query-transactions.png)

### IoT SIM Portal

![IoT search messages](docs/screenshots/iot-search-messages.png)

![IoT filter messages](docs/screenshots/iot-filter-messages.png)

![IoT delete message thread](docs/screenshots/iot-delete-message-thread.png)

![IoT get all messages](docs/screenshots/iot-get-all-messages.png)

![IoT send single message](docs/screenshots/iot-send-single-message.png)

![IoT delete message](docs/screenshots/iot-delete-message.png)

![IoT all SIMs](docs/screenshots/iot-all-sims.png)

![IoT lifecycle status](docs/screenshots/iot-lifecycle-status.png)

![IoT customer info](docs/screenshots/iot-customer-info.png)

![IoT SIM activation](docs/screenshots/iot-sim-activation.png)

![IoT activation trends](docs/screenshots/iot-activation-trends.png)

![IoT rename asset](docs/screenshots/iot-rename-asset.png)

![IoT location info](docs/screenshots/iot-location-info.png)

![IoT suspend or unsuspend subscriber](docs/screenshots/iot-suspend-unsuspend.png)

## Requirements

- PHP 8.2 or later
- Composer
- Yii2 dependencies installed with `composer install`
- A Safaricom Daraja sandbox or production app

## Setup

Install dependencies:

```bash
composer install
```

Create your local environment file:

```bash
copy .env.example .env
```

Then edit `.env` with your real Daraja credentials.

Required STK/Daraja values:

```env
DARAJA_ENVIRONMENT=sandbox
DARAJA_ENV=sandbox
DARAJA_CONSUMER_KEY=your_consumer_key
DARAJA_CONSUMER_SECRET=your_consumer_secret
DARAJA_SHORT_CODE=174379
DARAJA_SHORTCODE=174379
DARAJA_PASSKEY=your_stk_passkey
DARAJA_CALLBACK_BASE_URL=https://your-domain.example
```

Optional values:

```env
DARAJA_CALLBACK_SECRET=your_callback_secret
DARAJA_TEST_PHONE=254700000000
DARAJA_INITIATOR_NAME=your_initiator_name
DARAJA_INITIATOR_PASSWORD=your_initiator_password
DARAJA_SECURITY_CREDENTIAL=your_encrypted_security_credential
DARAJA_IOT_API_KEY=your_iot_api_key
DARAJA_IOT_MSISDN=254700000000
```

Start the app:

```bash
php -S localhost:8080 -t web
```

Open:

```text
http://localhost:8080
```

## How To Use

1. Open `http://localhost:8080`.
2. Click **Daraja** in the menu.
3. Select a service group and endpoint.
4. Fill in the visible fields.
5. Click **Send request**.
6. Review the response panel under the form.

For STK Push, use a phone number in `2547XXXXXXXX` format. The form hides `PartyA`, `PartyB`, and `Password` because the app derives them before sending the request.

## Endpoint Overrides

Some less common Daraja APIs can return `404` if Safaricom changes the sandbox path or exposes a different path in your Daraja portal. You can override a package endpoint path from `.env` without editing `vendor`.

Example:

```env
DARAJA_ENDPOINT_LIPA_NA_BONGA_CALCULATE_POINTS=/v1/lipa/na/bonga/calculator-points
```

The request page displays the full URL being called so you can compare it with the Safaricom portal.

## Troubleshooting

### STK Push: Invalid Password

Safaricom expects:

```text
base64_encode(BusinessShortCode + Passkey + Timestamp)
```

The app now generates that value automatically on submit. If the error persists, confirm:

- `DARAJA_PASSKEY` is correct.
- `BusinessShortCode` matches the passkey.
- `Timestamp` is in `YYYYMMDDHHMMSS` format.
- You are using the correct sandbox or production environment.

### Lipa na Bonga: HTTP 404

If Lipa na Bonga is enabled but returns `404`, compare the URL shown on the request page with the URL in your Daraja sandbox portal. If they differ, set an endpoint override in `.env`.

## Project Structure

```text
components/daraja/          Daraja service adapter
components/daraja/contracts Service interfaces
controllers/DarajaController.php
models/DarajaRequestForm.php
views/daraja/               Daraja service pages
docs/screenshots/           README screenshots
```

## Quality Checks

Run syntax and coding-standard checks:

```bash
php -l controllers/DarajaController.php
php -l models/DarajaRequestForm.php
vendor/bin/phpcs controllers models components views
```
