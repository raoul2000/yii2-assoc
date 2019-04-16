<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'link Address';
\yii\web\YiiAsset::register($this);
$contact = $model;
?>
<div>
    <div class="alert alert-info" role="alert">
        <p>
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Please select an address to link to this contact
        </p>
    </div>    
    <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'tableOptions'         => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $addressDataProvider,
            'filterModel' => $addressSearchModel,
            'columns' => [
                [
                    'class'     => 'yii\grid\ActionColumn',
                    'template'     => '{select}',
                    'buttons'   => [
                        'select' => function ($url, $address, $key) use ($contact) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-ok"></span>', 
                                ['contact/link-address', 'id'=> $contact->id, 'address_id' => $address->id],
                                ['title' => 'select this address', 'data-pjax'=>0]
                            );
                        },
                    ]
                ],
                'line_1',
                'line_2',
                'line_3',
                'zip_code',
                'city',
                'country',
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>