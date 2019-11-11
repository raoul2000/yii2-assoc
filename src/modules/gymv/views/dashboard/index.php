<?php
/* @var $this yii\web\View */
$this->registerCss(file_get_contents(__DIR__ . '/dashboard.css'));
?>
<div id="dashboard">
    <div id="widget-stat">
        <ul class="cardboard">
            <li class="card blue">
                <h1>Adhérents</h1>
                <div class="metric"><?= $membersCount ?></div>
                <div class="detail">
                    Vincennois et non Vincennois sur la saison
                </div>
            </li>
            <li class="card blue">
                <h1>Compte Courant</h1>
                <div class="metric"><?= $solde ?></div>
                <div class="detail">
                    Total débit = <?= $totalDeb ?>, Total crédit = <?= $totalCred ?>
                </div>
            </li>
            <li class="card blue">
                <h1>Cours Achetés</h1>
                <div class="metric"><?= $countCourses ?></div>
                <div class="detail">
                    Nombre total de cours sur la saison
                </div>
            </li>
        </ul>
    </div>
</div>
