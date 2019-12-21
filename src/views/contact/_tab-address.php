<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

$contactModel = $model;

?>
<?php if (!isset($model->address)):?>

    <p>No Address registered for this contact :</p>
    <?= Html::a(
        'Create Address',
        ['address/create', 'contact_id' => $model->id],
        ['class' => 'btn btn-success', 'data-pjax' => 0]
    ) ?>

    <?= Html::a(
        'Use an Existing Address',
        ['contact/link-address', 'id' => $model->id],
        ['class' => 'btn btn-primary', 'data-pjax' => 0]
    ) ?>

<?php else: ?>
    <p>
        <?= Html::a(
            'Create New Address For this Contact',
            ['address/create', 'contact_id' => $model->id, 'redirect_url' => Url::current()], 
            ['class' => 'btn btn-success', 'data-pjax' => 0]
        ) ?>
        <?= Html::a(
            'Update This Address',
            ['address/update', 'id' => $model->address->id, 'contact_id' => $model->id, 'redirect_url' => Url::current()],
            ['class' => 'btn btn-primary', 'data-pjax' => 0]
        ) ?>
        <?= Html::a(
            'Use Another Existing Address',
            ['contact/link-address', 'id' => $model->id, 'redirect_url' => Url::current()],
            ['class' => 'btn btn-primary', 'data-pjax' => 0]
        ) ?>
        <?= Html::a(
            'Leave This Address',
            ['contact/unlink-address', 'id' => $model->id],
            ['class' => 'btn btn-danger', 'data-pjax' => 0]
        ) ?>
        <?php if($searchUrl) : ?>
            <?= Html::a(
                'Search',
                $searchUrl,
                ['class' => 'btn btn-default', 'data-pjax' => 0, 'target' => 'blank']
            ) ?>
        <?php endif; ?>
    </p>
    
    <?= Yii::$app->formatter->asNoteBox($model->address->note) ?>
    
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
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                    . Html::encode($contact->longName),
                                ['contact/view', 'id' => $contact->id],
                                ['title' => 'view contact', 'data-pjax' => 0]
                            );
                        }
                        
                        return implode(' | ', $linkedContacts);
                    }
                }
            ],
        ],
    ]) ?>        
<?php endif; ?>