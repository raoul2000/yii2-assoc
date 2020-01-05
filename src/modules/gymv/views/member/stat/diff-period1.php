<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Contact;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Diff 2018-2019';
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['/gymv/member/home']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Statistics'), 'url' => ['/gymv/member/stat']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">

    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>

    <p>
        <?= \app\components\widgets\DownloadDataGrid::widget() ?>
    </p>

    <div class="alert alert-info">
        Liste des adhérents de la période 2018-2019 qui ne sont pas adhérent pour la période actuelle. <b>Attention</b>, cette liste
        n'est valide que si les adhérents de la période 2018-2019 ont été enregistrés dans la base de données.
    </div>

    <div style="margin-top:1em;">
        <?php Pjax::begin(); ?>
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
        <?php Pjax::end(); ?>
    </div>
</div>
