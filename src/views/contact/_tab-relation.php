<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div>
    <p>
        <?= Html::a('Create Relation', ['create', 'to_contact_id' => $model->id, 'redirect_url' => Url::current()], ['class' => 'btn btn-success']) ?>
    </p>
    <table class='table table-hover'>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Relation Type</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($relations as $relation) : ?>
                <tr>
                    <td>
                        <?= ( 
                            $relation->sourceContact->id == $model->id 
                            ?  Html::encode($relation->sourceContact->longName)
                            :  Html::a(
                                Html::encode($relation->sourceContact->longName),
                                ['contact/view', 'id' => $relation->sourceContact->id],
                                ['title' => 'View contact']
                            )
                        )?>
                    </td>
                    <td>
                        <i class="glyphicon glyphicon-chevron-right"></i>                    
                    </td>
                    <td>
                        <?= (
                            isset($relation->type) 
                            ? $relation->type 
                            : '...' 
                        )?>
                    </td>
                    <td>
                        <i class="glyphicon glyphicon-chevron-right"></i>                    
                    </td>
                    <td>
                        <?= ( 
                            $relation->targetContact->id == $model->id 
                            ?  Html::encode($relation->targetContact->longName)
                            :  Html::a(
                                Html::encode($relation->targetContact->longName),
                                ['contact/view', 'id' => $relation->targetContact->id],
                                ['title' => 'View contact']
                            )
                        )?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>