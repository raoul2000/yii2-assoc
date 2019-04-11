<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SessionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sessions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-index">

    <h1><?= Html::encode($this->title) ?> <small>Utilisateurs connectés</small></h1>
    <hr/>
    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                'expire:relativeTime',
                [
                    'attribute' => 'user_id',
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        // check record-history/index.php for same case : username in filter
                        return Html::encode($model->user->username . ' ( id = ' . $model->user_id . ')');
                    }
                ],
                [
                    'attribute' => 'last_write',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'  => '{view}'
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
