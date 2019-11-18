<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
//$this->registerCss(file_get_contents(__DIR__ . '/dashboard.css'));
?>
<div id="course-overview">
    <style>

        ul.item-list {
            /* Remove default list styling */
            list-style-type: none;
            padding: 0;
            margin: 0;
            margin-top:1em;
        }

        .item-list li a {
            border: 1px solid #ddd;
            margin-top: -1px; /* Prevent double borders */
            background-color: #f6f6f6; /* Grey background color */
            padding: 0.5em; /* Add some padding */
            text-decoration: none; /* Remove default text underline */
            font-size: 1em; /* Increase the font-size */
            color: black; /* Add a black text color */
            display: block; /* Make it into a block element to fill the whole list */
        }    
        .item-list li a:hover:not(.header) {
            background-color: #eee; /* Add a hover effect to all links, except for headers */
        }
        .item-list .product-name {
            width:100%;
        }

        .item-list .total-count {
            float:right;
            text-align:right;
        }
    </style>
    <div class="live-filter">
        <input id="live-filter-input" type="text" class="form-control" placeholder="Text input">
        <ul class="item-list">
            <?php foreach ($orders as $order) :?>
                <li>
                    <a href="http://www.google.fr">
                        <span class="product-name"><?= Html::encode($order['product']['name']) ?></span>
                        <span class="total-count badge"><?= $order['count_total'] ?></span>
                        <div class="clearfix"></div>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>    
    </div>

    <script>
        (function () {
            document.getElementById('live-filter-input').addEventListener('input', (ev) => {
                const filter = ev.target.value.toLowerCase();
                ev.target.parentElement.querySelectorAll('li  span.product-name').forEach( el => {
                    el.parentElement.style.display = el.textContent.trim().toLowerCase().indexOf(filter) > -1
                        ? ""
                        : "none"
                })
            });
        })();                
    </script>
</div>