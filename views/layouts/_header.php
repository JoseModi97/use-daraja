<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
    [
        'label' => 'Home',
        'url' => ['/site/index'],
    ],
    [
        'label' => 'Daraja',
        'url' => ['/daraja/index'],
        'items' => [
            [
                'label' => 'Overview',
                'url' => ['/daraja/index'],
            ],
            [
                'label' => 'STK Push',
                'url' => ['/daraja/request', 'endpoint' => 'stk.push'],
            ],
            [
                'label' => 'C2B',
                'url' => ['/daraja/request', 'endpoint' => 'c2b.register_url'],
            ],
            [
                'label' => 'Business Payments',
                'url' => ['/daraja/request', 'endpoint' => 'b2c.payment_request'],
            ],
            [
                'label' => 'Transactions',
                'url' => ['/daraja/request', 'endpoint' => 'transaction_status.query'],
            ],
            [
                'label' => 'Ratiba',
                'url' => ['/daraja/request', 'endpoint' => 'ratiba.create_paybill'],
            ],
            [
                'label' => 'Lipa na Bonga',
                'url' => ['/daraja/request', 'endpoint' => 'lipa_na_bonga.redeem_paybill'],
            ],
            [
                'label' => 'Subscriber Info',
                'url' => ['/daraja/request', 'endpoint' => 'imsi.check_ati'],
            ],
            [
                'label' => 'Pull Transactions',
                'url' => ['/daraja/request', 'endpoint' => 'pull_transactions.register'],
            ],
            [
                'label' => 'IoT SIM Portal',
                'url' => ['/daraja/request', 'endpoint' => 'iot.search_messages'],
            ],
        ],
    ],
    [
        'label' => 'About',
        'url' => ['/site/about'],
    ],
    [
        'label' => 'Contact',
        'url' => ['/site/contact'],
    ],
    [
        'label' => 'Login',
        'url' => ['/site/login'],
        'visible' => Yii::$app->user->isGuest,
    ],
    [
        'label' => 'Logout (' . Html::encode(Yii::$app->user->identity?->username ?? '') . ')',
        'url' => ['/site/logout'],
        'linkOptions' => [
            'data-method' => 'post',
            'class' => 'nav-link logout',
        ],
        'visible' => !Yii::$app->user->isGuest,
    ],
];

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?= Html::button(
        '&#127769;',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5',
            'aria-label' => 'Switch to dark mode',
        ],
    ) ?>
    <?php NavBar::end() ?>
</header>
