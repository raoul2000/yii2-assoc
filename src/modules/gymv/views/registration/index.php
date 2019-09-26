<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

?>
<div>
    <h1>Inscription</h1>
    <hr/>

    <p>
        Vous êtes sur le point de commencer une procédure d'inscription d'un nouvel adhérent, ou de re-inscription
        d'un ancien adhérent déjà présent dans la système.<br/>
        Cette procédure est simple, elle se découpe en 5 étapes à l'issue desquelles l'adhérent sera enregistré. 
    </p>

    <?= Html::a(
        "C'est parti ...",
        ['registration/contact-search'],
        ['class' => 'btn btn-primary']) 
    ?>
</div>