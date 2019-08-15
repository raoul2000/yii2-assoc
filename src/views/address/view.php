<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$title = $model->line_1 . ' ' . $model->line_2 . ' ' . $model->line_3;
$title = substr($title, 0, 30) . '...';
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Addresses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="address-view">

    <h1>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>

    <p>
        <?= Html::a(\Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(\Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => \Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(\Yii::t('app', 'Create Another Address'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'label' => \Yii::t('app', 'Used  By'),
                'format' => 'raw',
                'value' => function ($model) {
                    $count = count($model->contacts);
                    if ($count == 0) {
                        return '<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> ' 
                        . \Yii::t('app', 'this address is not used by any contact');
                    } else {
                        $linkedContacts = [];
                        foreach ($model->contacts as $contact) {
                            $linkedContacts[] = Html::a(
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                    . Html::encode($contact->longName),
                                ['contact/view', 'id' => $contact->id],
                                ['title' => \Yii::t('app', 'view contact')]
                            );
                        }
                        return implode(', ', $linkedContacts);
                    }
                }
            ],
        ],
    ]) ?>

</div>
