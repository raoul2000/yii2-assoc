<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div>
    <p>
        <?= Html::a(
            \Yii::t('app', 'Create Relation'), 
            ['contact-relation/create', 'source_contact_id' => $model->id, 'redirect_url' => Url::current()], 
            ['class' => 'btn btn-success']) 
        ?>
    </p>
    <table class='table table-hover'>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Relation Type</th>
                <th></th>
                <th></th>
                <th>From</th>
                <th>Until</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($relations as $relation) : ?>
                <tr>
                    <td>
                        <?= ( 
                            $relation->sourceContact->id == $model->id 
                            ?  '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($relation->sourceContact->longName)
                            :  Html::a(
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                    . Html::encode($relation->sourceContact->longName),
                                ['contact/view', 'id' => $relation->sourceContact->id, 'tab' => 'relation'],
                                ['title' => 'View contact']
                            )
                        )?>
                    </td>
                    <td>
                        <i class="glyphicon glyphicon-chevron-right"></i>                    
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asText(app\components\Constant::getContactRelationName($relation->type)) ?>
                    </td>
                    <td>
                        <i class="glyphicon glyphicon-chevron-right"></i>                    
                    </td>
                    <td>
                        <?= ( 
                            $relation->targetContact->id == $model->id 
                            ?  '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($relation->targetContact->longName)
                            :  Html::a(
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                    . Html::encode($relation->targetContact->longName),
                                ['contact/view', 'id' => $relation->targetContact->id, 'tab' => 'relation'],
                                ['title' => 'View contact']
                            )
                        )?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asAppDate($relation->valid_date_start) ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asAppDate($relation->valid_date_end) ?>
                    </td>
                    <td>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            ['contact-relation/view', 'id' => $relation->id],
                            ['title' => 'view relation']
                        )?>
                    </td>                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>