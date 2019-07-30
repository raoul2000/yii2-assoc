<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Contact;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">

    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>
    
    <p>
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Create <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><?= Html::a('Person', ['create', 'person' => true]) ?></li>
                <li><?= Html::a('Organization', ['create', 'person' => false]) ?></li>
            </ul>
        </div>    
        <?= Html::a('Export CSV', ['export-csv'], ['class' => 'btn btn-default',  'data-pjax'=>0]) ?>
        <?= Html::a('<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Statistics', ['stat/contact'], ['class' => 'btn btn-default',  'data-pjax'=>0]) ?>
        <?= Html::a('<span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> Quality', ['quality/contact'], ['class' => 'btn btn-default',  'data-pjax'=>0]) ?>
        <div class="pull-right">
            <?= Html::a('Manage Contact Relations', ['contact-relation/index'], ['class' => 'btn btn-info',  'data-pjax'=>0]) ?>
        </div>
    </p>

    <?= yii\bootstrap\Nav::widget([
        'options' => ['class' =>'nav-tabs'],
        'items' => [
            [
                'label' => 'Person',
                'encode' => false,
                'url' => ['index', 'tab'=>'person'],
                'active' => $tab == 'person'
            ],
            [
                'label' => 'Organization',
                'url' => ['index', 'tab'=>'organization'],
                'active' => $tab == 'organization'
            ],
        ]
    ]) ?>

    <div style="margin-top:1em;">
        <?php Pjax::begin(); ?>
            <?php if ($tab == 'person'):?>
                <?= GridView::widget([
                    'tableOptions' => ['class' => 'table table-hover table-condensed'],
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'name',
                            'label' => 'Name'
                        ],
                        [
                            'attribute' => 'firstname',
                            'label' => 'Firstname'
                        ],
                        'email:email',
                        [
                            'attribute' => 'gender',
                            'label' => 'Gender',
                            'filter' => [
                                Contact::GENDER_MALE => 'man',
                                Contact::GENDER_FEMALE => 'woman'
                            ],
                            'format'    => 'gender'
                        ],
                        //'birthday:date',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'contentOptions' => ['nowrap' => 'nowrap']
                        ],
                    ],
                ]); ?>

            <?php elseif ($tab == 'organization'): ?>
                <?= GridView::widget([
                    'tableOptions' => ['class' => 'table table-hover table-condensed'],
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'name',
                            'label' => 'Raison Sociale'
                        ],
                        [
                            'attribute' => 'firstname',
                            'label' => 'Complément'
                        ],
                        'email:email',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'contentOptions' => ['nowrap' => 'nowrap']
                        ],
                    ],
                ]); ?>
            <?php endif; ?>
        <?php Pjax::end(); ?>
    </div>
</div>
