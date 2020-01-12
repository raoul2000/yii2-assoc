<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="filter-category" style="margin-bottom:1em;">
    <?php $form = ActiveForm::begin([
        'method'  => 'GET'
    ]); ?>
        <table width="100%">
            <tr>
                <td style="width: 100%;padding-right: 1em;">
                    <?= \dosamigos\selectize\SelectizeTextInput::widget([
                            'name'          => 'category_filter',
                            'value'         => $category_filter,
                            'options'       => ['class' => 'form-control'],
                            'clientOptions' => [
                                'plugins'     => ['remove_button'],
                                'valueField'  => 'id',
                                'labelField'  => 'name',
                                'searchField' => ['name'],
                                'create'      => false,
                                'placeholder' => 'categorie ...',
                                'maxItems'    => 10,
                                'options'     => $categoryOptions
                            ],
                        ])
                    ?>
                </td>
                <td>
                    <?= Html::submitButton(
                        '<span class="glyphicon glyphicon-search" aria-hidden="true"></span> '
                            . \Yii::t('app', 'Search'), 
                        ['class' => 'btn btn-primary']
                    ) ?>
                </td>
            </tr>
        </table>
    <?php ActiveForm::end(); ?>
</div>
