<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'dataFile')->fileInput() ?>
        <?= Html::submitButton('Enregistrement', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
</div>
