<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="address-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'line_1',
            'line_2',
            'line_3',
            'zip_code',
            'city',
            'country',
            'note',
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'label' => 'Used  By',
                'format' => 'raw',
                'value' => function ($model) {
                    $count = count($model->contacts);
                    if ($count == 0) {
                        return '<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> this address is not used by any contact';
                    } else {
                        $linkedContacts = [];
                        foreach ($model->contacts as $contact) {
                            $linkedContacts[] = Html::a(
                                Html::encode($contact->name),
                                ['contact/view', 'id' => $contact->id],
                                ['title' => 'view Contact']
                            );
                        }
                        return implode(', ', $linkedContacts);
                    }
                }
            ],
        ],
    ]) ?>

</div>
