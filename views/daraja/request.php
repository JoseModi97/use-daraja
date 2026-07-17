<?php

declare(strict_types=1);

/** @var app\models\DarajaRequestForm $model */
/** @var array $endpoint */
/** @var array $endpointOptions */
/** @var array $fieldHints */
/** @var string $fullEndpointUrl */
/** @var array $groups */
/** @var array|null $result */
/** @var string|null $serviceNotice */
/** @var yii\web\View $this */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;

$this->title = $endpointOptions[$model->endpointKey] ?? 'Daraja Request';
$this->params['breadcrumbs'][] = ['label' => 'Daraja Services', 'url' => ['/daraja/index']];
$this->params['breadcrumbs'][] = $this->title;

$field = static function (
    string $group,
    string $name,
    mixed $value,
    array $hints,
): string {
    $id = 'daraja-' . $group . '-' . preg_replace('/[^a-z0-9_-]+/i', '-', $name);
    $label = Inflector::camel2words($name);
    $inputName = 'DarajaRequestForm[' . $group . '][' . $name . ']';
    $type = 'text';
    $options = [
        'id' => $id,
        'class' => 'form-control',
        'placeholder' => $label,
    ];

    if (str_contains(strtolower($name), 'url')) {
        $type = 'url';
    } elseif (
        str_contains(strtolower($name), 'amount')
        || str_contains(strtolower($name), 'points')
        || in_array($name, ['pageNo', 'pageSize'], true)
    ) {
        $type = 'number';
    } elseif (str_contains(strtolower($name), 'password') || str_contains(strtolower($name), 'credential')) {
        $type = 'password';
        $options['autocomplete'] = 'off';
    } elseif (str_contains(strtolower($name), 'date')) {
        $type = 'text';
        $options['placeholder'] = $name === 'StartDate' || $name === 'EndDate' ? 'YYYYMMDD or YYYY-MM-DD HH:MM:SS' : $label;
    }

    $hint = $hints[$name] ?? null;

    return Html::tag(
        'div',
        Html::label(Html::encode($label), $id, ['class' => 'form-label fw-semibold small'])
        . Html::input($type, $inputName, (string) $value, $options)
        . ($hint ? Html::tag('div', Html::encode($hint), ['class' => 'form-text']) : ''),
        ['class' => 'col-md-6'],
    );
};
?>
<div class="daraja-request">
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="list-group daraja-sidebar">
                <?php foreach ($groups as $group): ?>
                    <div class="list-group-item bg-body-tertiary fw-semibold">
                        <?= Html::encode($group['label']) ?>
                    </div>
                    <?php foreach ($group['endpoints'] as $key => $label): ?>
                        <?= Html::a(
                            Html::encode($label),
                            ['/daraja/request', 'endpoint' => $key],
                            [
                                'class' => 'list-group-item list-group-item-action'
                                    . ($key === $model->endpointKey ? ' active' : ''),
                            ],
                        ) ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </aside>

        <section class="col-lg-9">
            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
                <div>
                    <h1 class="h3 mb-2"><?= Html::encode($this->title) ?></h1>
                    <p class="text-body-secondary mb-0">
                        <?= Html::encode($endpoint['method'] ?? 'POST') ?>
                        <code><?= Html::encode($endpoint['path'] ?? '') ?></code>
                    </p>
                </div>
                <?= Html::a(
                    'All services',
                    ['/daraja/index'],
                    ['class' => 'btn btn-outline-secondary'],
                ) ?>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <?php if ($serviceNotice): ?>
                        <div class="alert alert-warning">
                            <?= Html::encode($serviceNotice) ?>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-secondary">
                        Calling <code><?= Html::encode($fullEndpointUrl) ?></code>
                    </div>

                    <?php $form = ActiveForm::begin(['id' => 'daraja-request-form']); ?>

                    <?= $form->field($model, 'endpointKey')->dropDownList(
                        $endpointOptions,
                        [
                            'class' => 'form-select',
                            'onchange' => 'window.location.href = "?r=daraja/request&endpoint=" + encodeURIComponent(this.value)',
                        ],
                    ) ?>

                    <h2 class="h5 mt-4 mb-3">Payment Details</h2>
                    <div class="row g-3">
                        <?php foreach ($model->payload as $name => $value): ?>
                            <?php
                            // STK Push derives PartyA from PhoneNumber and PartyB from BusinessShortCode.
                            if ($model->endpointKey === 'stk.push' && in_array($name, ['PartyA', 'PartyB'], true)) {
                                continue;
                            }

                            // STK password is generated at submit time from shortcode, passkey, and timestamp.
                            if (in_array($model->endpointKey, ['stk.push', 'stk.query'], true) && $name === 'Password') {
                                continue;
                            }
                            ?>
                            <?= $field('payload', (string) $name, $value, $fieldHints) ?>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($model->query)): ?>
                        <h2 class="h5 mt-4 mb-3">Query Options</h2>
                        <div class="row g-3">
                            <?php foreach ($model->query as $name => $value): ?>
                                <?= $field('query', (string) $name, $value, $fieldHints) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($model->headers)): ?>
                        <h2 class="h5 mt-4 mb-3">IoT Headers</h2>
                        <div class="row g-3">
                            <?php foreach ($model->headers as $name => $value): ?>
                                <?= $field('headers', (string) $name, $value, $fieldHints) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center justify-content-end gap-2 mt-4">
                        <?= Html::a(
                            'Reset sample',
                            ['/daraja/request', 'endpoint' => $model->endpointKey],
                            ['class' => 'btn btn-outline-secondary'],
                        ) ?>
                        <?= Html::submitButton(
                            'Send request',
                            ['class' => 'btn btn-primary'],
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <?php if ($result !== null): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header <?= $result['ok'] ? 'bg-success-subtle' : 'bg-danger-subtle' ?>">
                        <strong><?= $result['ok'] ? 'Response' : 'Request failed' ?></strong>
                    </div>
                    <div class="card-body">
                        <pre class="daraja-response mb-0"><?= Html::encode(Json::encode(
                            $result,
                            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                        )) ?></pre>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
