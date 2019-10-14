<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-csv-result">
    <h1>Import Product <small>result</small></h1>
    <hr/>

    <?php if (!empty($errorMessage)) :?>
        <div class="alert alert-danger">
            <?= Html::encode($errorMessage) ?>
        </div>
    <?php endif; ?>
    

    <h2>Categories</h2>
    <?php if (!empty($categoriesInserted)): ?>

        <table class="table">
            <tbody>
                <?php foreach($categoriesInserted as $category): ?>
                    <tr>
                        <td><?= Html::encode($category->id) ?></td>
                        <td><?= Html::encode($category->name) ?></td>
                        <td>
                            <?php if($category->hasErrors()): ?>
                                <?= Html::errorSummary($category) ?>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    Category created
                                </div>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">
            No new category created
        </div>
    <?php endif; ?>


    <h2>Products</h2>
    <?php if (!empty($productInserted)): ?>
        <table class="table">
            <tbody>
                <?php foreach($productInserted as $product): ?>
                    <tr>
                        <td><?= Html::encode($product->id) ?></td>
                        <td><?= Html::encode($product->name) ?></td>
                        <td><?= $product->value ?></td>
                        <td>
                            <?php if($product->hasErrors()): ?>
                                <div class="alert alert-danger">
                                    <?= Html::errorSummary($product) ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    Product created
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">
            No new product created
        </div>
    <?php endif; ?>


</div>
