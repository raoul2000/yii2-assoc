<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Attachment */

?>
<div class="row">
    <div class="col-sm-5">
        <div class="attachment-form">

            <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'category_id')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>

                <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Cancel', $redirect_url, ['class' => 'btn btn-default']) ?>
                </div>

            <?php ActiveForm::end(); ?>

        </div>    
    </div>
    <div class="col-sm-7">
        <?= Html::tag('iframe', '', [
        'style' => 'width: 100%;height: calc(100vh - 80px);',
        'src' => Url::toRoute(['preview-attachment', 'id' => $model->id])
        ]) ?>
    </div>
</div>



