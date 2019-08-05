<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = $model->longName;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$contactModel = $model;
?>
<div class="contact-view">

    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
        <small><?= ($model->is_natural_person == true ? 'Person' : 'Organization') ?></small>
    </h1>

    <hr/>
    
    <div style="margin-bottom:1em">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>

        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Create Another Contact <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><?= Html::a('Person', ['create', 'person' => true]) ?></li>
                <li><?= Html::a('Organization', ['create', 'person' => false]) ?></li>
            </ul>
        </div> 
    </div>

    <?php if ($model->is_natural_person):?>
        <div class="row">
            <div class="col-lg-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',
                        'firstname',
                        'email:email',
                        'gender:gender',
                        [
                            'attribute' => 'birthday',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if (!empty($model->birthday)) {
                                    // convert from App to DB format
                                    $birthday = \app\components\helpers\DateHelper::toDateDbFormat($model->birthday);
                                    return Yii::$app->formatter->asDate($birthday) . ' (' . Yii::$app->formatter->asAge($birthday) . ' ans)';
                                } else {
                                    return null;
                                }
                            }
                        ],
                    ],
                ]) ?>
            </div>
            <div class="col-lg-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'phone_1:clickToCall',
                        'phone_2:clickToCall',
                        [
                            'attribute' => 'updated_at',
                            'format' => ['date', 'php:d/m/Y H:i']
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['date', 'php:d/m/Y H:i']
                        ],
                        [
                            'label' => 'History',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a('(view)', \app\models\RecordHistory::getRecordHistoryIndex(\app\models\Contact::tableName(), $model->id));
                            }
                        ],
                    ],
                ]) ?>            
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',
                        'firstname',
                        'email:email',
                    ],
                ]) ?>
            </div>
            <div class="col-lg-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'phone_1:clickToCall',
                        'phone_2:clickToCall',
                        [
                            'attribute' => 'updated_at',
                            'format' => ['date', 'php:d/m/Y H:i']
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['date', 'php:d/m/Y H:i']
                        ],
                        [
                            'label' => 'History',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a('(view)', \app\models\RecordHistory::getRecordHistoryIndex(\app\models\Contact::tableName(), $model->id));
                            }
                        ],
                    ],
                ]) ?>

            </div>
        </div>                    
    <?php endif; ?>

        <?= yii\bootstrap\Nav::widget([
            'options' => ['class' =>'nav-tabs'],
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-home" aria-hidden="true"></span> Address',
                    'encode' => false,
                    'url' => ['view', 'id' => $model->id,'tab'=>'address'],
                    'active' => $tab == 'address'
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> Account',
                    'encode' => false,
                    'url' => ['view', 'id' => $model->id,'tab'=>'account'],
                    'active' => $tab == 'account'
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Attachment',
                    'encode' => false,
                    'url' => ['view', 'id' => $model->id,'tab'=>'attachment'],
                    'active' => $tab == 'attachment'
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Orders',
                    'encode' => false,
                    'url' => ['view', 'id' => $model->id,'tab'=>'order'],
                    'active' => $tab == 'order'
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> Relations',
                    'encode' => false,
                    'url' => ['view', 'id' => $model->id,'tab'=>'relation'],
                    'active' => $tab == 'relation'
                ],
            ]
        ]) ?>

    <div style="margin-top:1em;">
        <?= $tabContent ?>
    </div>
 
</div>
