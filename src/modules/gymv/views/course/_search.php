<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                            'name' => 'OrderSearch[product_id]',
                            'items' => $products,
                            'value' => $searchModel->product_id,
                            'options' => ['class' => 'form-control'],
                            'clientOptions' => [
                                'create' => false,
                                'placeholder' => \Yii::t('app', 'select course ...'),
                            ],
                        ])
                    ?>
                </td>
                <td>
                    <?= Html::submitButton(
                        '<span class="glyphicon glyphicon-search" aria-hidden="true"></span> ' 
                            . \Yii::t('app', 'Search'),
                        ['class' => 'btn btn-primary']
                    )?>
                </td>
            </tr>
        </table>
    <?php ActiveForm::end(); ?>
</div>
