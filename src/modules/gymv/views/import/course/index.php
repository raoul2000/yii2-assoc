<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product';
$this->params['breadcrumbs'][] = ['label' => 'Gymv', 'url' => ['/gymv']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Import'), 'url' => ['/gymv/import/home']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">
    <h1>Product Cours</h1>
    <hr/>
    <div class="row">
        <div class="col-sm-6">
            <p>
                Importe la liste des contact ayant souscrit à un cours :
                <ul>
                    <li>Encoding is UTF-8</li>
                    <li>col 1 : Nom</li>
                    <li>col 2 : Prénom</li>
                </ul>
            </p>
        </div>
        <div class="col-sm-6">
            <div class="well">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                    <?= $form->field($model, 'dataFile')->fileInput() ?>
                    <?= Html::submitButton(\Yii::t('app', 'Import'), ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
