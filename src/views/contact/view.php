<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$contactModel = $model;
$allAttachments = $model->attachments;

?>
<div class="contact-view">

    <h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Create Another Contact', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('View Orders', ['order/index', 'OrderSearch[contact_id]' => $model->id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('View Accounts', ['bank-account/index', 'BankAccountSearch[contact_id]' => $model->id], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Bank Account',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->bankAccounts) {
                        return count($model->bankAccounts)
                            . ' '
                            . Html::a('(view)', ['bank-account/index','BankAccountSearch[contact_id]' => $model->id]);
                    } else {
                        return 'No account' ;
                    }
                }
            ],
            'name',
            'firstname',
            'gender:gender',
            'birthday:date',
            'is_natural_person:boolean',
            'email:email',
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
        ],
    ]) ?>

    <h2>Attachment</h2>
    <hr/>
    <p>
        <?= Html::a('Add Attachment', ['create-attachment', 'id' => $model->id, 'redirect_url' => Url::current() ], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php if (count($allAttachments) == 0): ?>
        no attachment
    <?php else: ?>
        <?= GridView::widget([
            'tableOptions' 		=> ['class' => 'table table-hover table-condensed'],
            'dataProvider' => new ArrayDataProvider(['allModels' => $model->attachments]),
            'layout' => '{items}',
            'columns' => [
                'name',
                'note',
                [
                    'attribute' => 'updated_at',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],
                [
                    'class'     => 'yii\grid\ActionColumn',
                    'template'  => '{preview} {update} {download} {delete} ',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'delete') {
                            return Url::to(['delete-attachment', 'id' => $model->id, 'redirect_url' => Url::current() ]);
                        } elseif ($action == 'download') {
                            return Url::to(['download-attachment', 'id' => $model->id]);
                        } elseif ($action == 'preview') {
                            return Url::to(['preview-attachment', 'id' => $model->id]);
                        } elseif ($action == 'update') {
                            return Url::to(['update-attachment', 'id' => $model->id, 'redirect_url' => Url::current()]);
                        }
                        return Url::to(['order/' . $action, 'id' => $model->id]);
                    },
                    'buttons'   => [
                        'download' => function ($url, $attachment, $key) use ($contactModel) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-download-alt"></span>',
                                $url,
                                ['title' => 'download', 'data-pjax'=>0]
                            );
                        },
                        'preview' => function ($url, $attachment, $key) use ($contactModel) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                $url,
                                ['title' => 'preview in a new window', 'target' => '_blank', 'data-pjax'=>0]
                            );
                        },
                    ]
                ],
            ],
        ]); ?> 
    <?php endif; ?>

    <h2>Address</h2>
    <hr/>
    <?php if (!isset($model->address)): ?>

        <p>No Address registered for this contact :</p>
        <?= Html::a('Create Address', ['address/create', 'contact_id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php if ($addressCount != 0 ): ?>
            <?= Html::a('Use an Existing Address', ['contact/link-address', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif;?>
    
    <?php else: ?>
        <p>
            <?= Html::a('Create New Address For this Contact', ['address/create', 'contact_id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Update This Address', ['address/update', 'id' => $model->address->id, 'contact_id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Use Another Existing Address', ['contact/link-address', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Leave This Address', ['contact/unlink-address', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
        </p>
        <?= DetailView::widget([
            'model' => $model->address,
            'attributes' => [
                'line_1',
                'line_2',
                'line_3',
                'zip_code',
                'city',
                'country',
                'note',
                [
                    'attribute' => 'updated_at',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],
                [
                    'attribute' => 'created_at',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],
                [
                    'label' => 'Also Used By',
                    'format' => 'raw',
                    'value' => function ($model) use ($contactModel) {
                        $count = count($model->contacts);
                        if ($count == 1) {
                            return '(not used by another contact)';
                        } else {
                            $linkedContacts = [];
                            foreach ($model->contacts as $contact) {
                                if ($contactModel->id == $contact->id) {
                                    continue;
                                }
                                $linkedContacts[] = Html::a(
                                    Html::encode($contact->name),
                                    ['contact/view', 'id' => $contact->id],
                                    ['title' => 'view Contact']
                                );
                            }
                            
                            return implode(', ', $linkedContacts);
                        }
                    }
                ],
            ],
        ]) ?>        
    <?php endif; ?>
</div>
