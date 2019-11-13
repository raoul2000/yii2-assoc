<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-search" style="margin-bottom:1em;">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'form'
        ],
    ]); ?>
        <table width="100%">
            <tr>
                <td style="width: 100%;padding-right: 1em;">
                    <?= \dosamigos\selectize\SelectizeTextInput::widget([
                            'name' => 'products',
                            'value' => $products,
                            'loadUrl' => ['query-tags'],
                            'options' => ['class' => 'form-control'],
                            'clientOptions' => [
                                'plugins' => ['remove_button'],
                                'valueField' => 'name',
                                'labelField' => 'name',
                                'searchField' => ['name'],
                                'create' => false,
                                'placeholder' => \Yii::t('app', 'select course ...')
                            ],
                        ])
                    ?>
                </td>
                <td>
                    <?= Html::submitButton('Search', ['class' => 'btn btn-default']) ?>
                </td>
            </tr>
        </table>
    <?php ActiveForm::end(); ?>
</div>
