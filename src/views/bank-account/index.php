<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BankAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bank Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-account-index">

    <h1>
        <span class="glyphicon glyphicon-euro" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <?php Pjax::begin(); ?>
        <p>
            <?= Html::a('Create Bank Account', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'contact_id',
                    'filter'    =>  $contacts,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($model->contact_name),
                            ['contact/view','id'=>$model->contact_id],
                            ['data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                        );
                    }
                ],
                'name',
                'initial_value',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['nowrap' => 'nowrap']
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
