<?php

declare(strict_types=1);

/** @var array $groups */
/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Daraja Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="daraja-index">
    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-4">
        <div>
            <h1 class="h2 mb-2"><?= Html::encode($this->title) ?></h1>
            <p class="text-body-secondary mb-0">
                Choose a Safaricom Daraja service, edit its request JSON, and send it from this app.
            </p>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($groups as $id => $group): ?>
            <section id="<?= Html::encode($id) ?>" class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm daraja-card">
                    <div class="card-body">
                        <h2 class="h5 mb-3"><?= Html::encode($group['label']) ?></h2>
                        <div class="list-group list-group-flush">
                            <?php foreach ($group['endpoints'] as $endpoint => $label): ?>
                                <?= Html::a(
                                    Html::encode($label),
                                    ['/daraja/request', 'endpoint' => $endpoint],
                                    ['class' => 'list-group-item list-group-item-action px-0'],
                                ) ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</div>
