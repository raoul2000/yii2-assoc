<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = 'Create Contact';
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$subTitle = '';
if (isset($person)) {
    $subTitle = '<small>' . ($person == true ? 'Person' : 'Organization') . '</small>';
} 
?>
<div class="contact-create">

    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
        <?= $subTitle ?>
    </h1>
    <hr/>
    <?php if ( !isset($person) ) :?>
        <p>
            Choose the type of contact you want to create :
            <ul>
                <li>
                    <?= Html::a('A Person', ['contact/create', 'person' => true]) ?>
                </li>
                <li>
                    <?= Html::a('A Company/Organisation', ['contact/create', 'person' => false]) ?>
                </li>
            </ul>
        </p>
    <?php else: ?>
        <?= $this->render('_form-' . ($person == true ? 'person' : 'organization'), [
            'model' => $model,
        ]) ?>
    <?php endif; ?>

</div>
