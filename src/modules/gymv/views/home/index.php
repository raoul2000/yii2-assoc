<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Contact;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<div>

    <p>
        <?= Html::a('Enregistrement', ['cart/check-out'], ['class' => 'btn btn-primary']) ?>
        <?php 
        // echo  Html::a('Import I-Reseau', ['contact-import/index'], ['class' => 'btn btn-primary']) 
        ?>
    </p>
    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
        <?= \Yii::t('app', 'Registered Contacts') ?>
    </h1>    
    <hr/>

    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'note',
                    'filter'    => false,
                    'label'     => '',
                    'format'    => 'note'
                ],
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
                    'class' => 'yii\grid\ActionColumn',
                    'template'  => '{view}',
                    'contentOptions' => ['nowrap' => 'nowrap'],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'view') {
                            return Url::to([
                                '/contact/view',
                                'id' => $model->id
                            ]);
                        }
                    },
                ],                        
            ],
            ]); 
        ?>    
    <?php Pjax::end(); ?>                

</div>
