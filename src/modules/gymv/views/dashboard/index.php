<?php
/* @var $this yii\web\View */
$this->registerCss(file_get_contents(__DIR__ . '/dashboard.css'));
?>
<div id="dashboard">
    <div id="widget-stat">
        <ul class="cardboard">
            <li class="card blue">
                <h1>Adhérents</h1>
                <div class="metric">433</div>
                <div class="detail">
                    percent update 1 hour ago
                </div>
            </li>
            <li class="card blue">
                <h1>Compte Courant</h1>
                <div class="metric">2334 eurs</div>
                <div class="detail">
                    Total débit = 2334, Total crédit = 3345,45 sdf sdf
                </div>
            </li>
            <li class="card blue">
                <h1>Cours Achetés</h1>
                <div class="metric">570</div>
                <div class="detail">
                    Nombre total de cours sur la saison
                </div>
            </li>
        </ul>
    </div>
</div>
