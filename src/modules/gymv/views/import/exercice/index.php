<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Exercice';
$this->params['breadcrumbs'][] = ['label' => 'Gymv', 'url' => ['/gymv']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Import'), 'url' => ['/gymv/import/home']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <h1>Exercice <small>Transactions Compte</small></h1>
    <hr/>
    <div class="row">
        <div class="col-lg-6">
            <p>
                Import des transactions Compte Courant. <br/>Liste des Colonnes :
                <ul>
                    <li><b>Date</b> : format jj/mm/AA</li>
                    <li><b>Num</b> : format integer|String</li>
                    <li><b>Désignation</b> : format string</li>
                    <li><b>RECETTES</b> : format numérique</li>
                    <li><b>DEPENSES</b> : format numérique</li>
                </ul>
            </p>
            <p>
                Exemple : <br/>
                <pre>
"Date","Num","Désignation","RECETTES","DEPENSES"
12/09/19,"VIR","Virement solde LCL - compte courant","2524,52",
13/09/19,1,"Remise 3 chèques adhésions",667,
18/09/19,"VIR","Ouverture livret bleu - épargne",,20000
23/09/19,"VIR","Codep - réafiliation Vitafédé",,90
21/09/19,2,"Remise 8 chèques adhésions",1594,
etc ...
                </pre>
            </p>
        </div>
        <div class="col-lg-6">
            <div class="well">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                    <?= $form->field($model, 'dataFile')->fileInput() ?>
                    <?= Html::submitButton(\Yii::t('app', 'Import'), ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>


