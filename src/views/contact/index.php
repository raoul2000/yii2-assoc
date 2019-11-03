<?php

use yii\helpers\Html;
use yii\helpers\Url;
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
                <?= \Yii::t('app', 'Create') ?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><?= Html::a(\Yii::t('app', 'Person'), ['create', 'person' => true]) ?></li>
                <li><?= Html::a(\Yii::t('app', 'Organization'), ['create', 'person' => false]) ?></li>
            </ul>
        </div>    
        <?= \app\components\widgets\DownloadDataGrid::widget() ?>
        <?= Html::a('<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> ' . \Yii::t('app', 'Statistics'), ['stat/contact'], ['class' => 'btn btn-default',  'data-pjax'=>0]) ?>
        <?= Html::a('<span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> ' . \Yii::t('app', 'Quality'), ['quality/contact', 'tab' => 'analysis'], ['class' => 'btn btn-default',  'data-pjax'=>0]) ?>
        <div class="pull-right">
            <?= Html::a(\Yii::t('app', 'Manage Contact Relations'), ['contact-relation/index'], ['class' => 'btn btn-info',  'data-pjax'=>0]) ?>
        </div>
    </p>

    <?php  echo $this->render(
        '_search', 
        [
            'model' => $searchModel, 
            'tagValues' => $tagValues,
            'tab' => $tab
        ]
    ); ?>
    
    <?= yii\bootstrap\Nav::widget([
        'options' => ['class' =>'nav-tabs'],
        'items' => [
            [
                'label' => \Yii::t('app', 'Person'),
                'encode' => false,
                'url' => ['index', 'tab'=>'person'],
                'active' => $tab == 'person'
            ],
            [
                'label' => \Yii::t('app', 'Organization'),
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
                    'filterModel'  => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'note',
                            'filter'    => false,
                            'label'     => '',
                            'format'    => 'note'
                        ],
                        [
                            'attribute' => 'name',
                            'label' => \Yii::t('app', 'Name')
                        ],
                        [
                            'attribute' => 'firstname',
                            'label' => \Yii::t('app', 'Firstname')
                        ],
                        'email:email',
                        [
                            'attribute' => 'gender',
                            'label' => \Yii::t('app', 'Gender'),
                            'filter' => [
                                Contact::GENDER_MALE   => \Yii::t('app', 'man'),
                                Contact::GENDER_FEMALE => \Yii::t('app', 'woman')
                            ],
                            'format'    => 'gender'
                        ],
                        'birthday:appDate',
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
                            'label' => \Yii::t('app', 'Raison Sociale')
                        ],
                        [
                            'attribute' => 'firstname',
                            'label' => \Yii::t('app', 'Complément')
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
