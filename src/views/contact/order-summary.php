<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use \yii\helpers\VarDumper;

$this->title = $model->longName;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->longName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Orders Summary';
\yii\web\YiiAsset::register($this);

?>
    <h2>
        Orders Summary
        <small>for <?= Html::a(Html::encode($model->longName), ['view', 'id' => $model->id], ['title' => 'view contact']) ?></small>
    </h2>

    <hr/>

<?php
// interval structure : [ start, end] with start < end

function inInterval($value, $interval) {
    return $value >= $interval[0] && $value <= $interval[1];
}
/**
 * Checks if an interval is included in another one
 * $intA is included in $intB if both the limits of intA are included in intB
 * 
 * intervalA : -----------|***|----------
 * intervalB : ------|****************|--
 * 
 * @param [int,int] $intervalA
 * @param [int,int] $intervalB
 * @return bool 
 */
function included($intervalA, $intervalB) {
    return inInterval($intervalA[0], $intervalB) && inInterval($intervalA[1], $intervalB);
}

/**
 * Returns TRUE if intervalA fully contains interval B.
 * 
 * intervalA : ------|****************|--
 * intervalB : -----------|***|----------
 *
 * @param [type] $intervalA
 * @param [type] $intervalB
 * @return void
 */
function contains($intervalA, $intervalB) {
    //return $intervalA[0] < $intervalB[0] && $intervalA[1] > $intervalB[1];
    return included($intervalB, $intervalA);
}
/**
 * Checks if an interval overlaps another one
 * intervalA overlaps intervalB if one of its limits is included in intervalB
 * Note that :
 * included(A,B) = true ==> overlap(A,B)
 * @param [int, int] $intervalA
 * @param [int, int] $intervalB
 * @return bool
 */
function overlap($intervalA, $intervalB) {
    return overlapLeft($intervalA, $intervalB) || overlapRight($intervalA, $intervalB);
}
/**
 * 
 * intervalA : ----|******|---------------------
 * intervalB : --------|*********|--------------

 * @param [type] $intervalA
 * @param [type] $intervalB
 * @return void
 */
function overlapLeft($intervalA, $intervalB) {
    return inInterval($intervalA[1], $intervalB);
}
/**
 * intervalA : ----------------|******|---------
 * intervalB : --------|*********|--------------
 *
 * @param [type] $intervalA
 * @param [type] $intervalB
 * @return void
 */
function overlapRight($intervalA, $intervalB) {
    return inInterval($intervalA[0], $intervalB);
}

/**
 * Create an interval by joining 2 intervals and even if they have no value in common
 * @param [int, int] $intervalA
 * @param [int, int] $intervalB
 * @return [int, int]
 */
function joinInterval($intervalA, $intervalB) {
    return [
        min($intervalA[0], $intervalB[0]),
        max($intervalA[1], $intervalB[1])
    ];
}

function add($intervalA, $intervalB) {
    if (overlap($intervalA, $intervalB) || overlap($intervalB, $intervalA)) {
        return joinInterval($intervalA, $intervalB);
    } else {
        return [
            $intervalA,
            $intervalB
        ];
    }
}

/**
 * compute intervalA - intervalB
 *
 * @param [type] $intervalA
 * @param [type] $intervalB
 * @return void
 */
function substract($intervalA, $intervalB) {
    if( contains($intervalB, $intervalA)) {
        // A : ------|*****|--------------
        // B : ----|**********|-----------
        return [];
    } elseif ( included($intervalB, $intervalA)) {
        // A : ------|**************|------
        // B : ----------|******|----------
        return [
            [$intervalA[0], $intervalB[0]],
            [$intervalB[1], $intervalA[1]]
        ];
    } elseif (!overlap($intervalB, $intervalA)) {
        // A : ------|*****|--------------
        // B : ----------------|****|-----
        return $intervalA;
    } elseif ( overlapLeft($intervalB, $intervalA )) {
        // A : ------|******|-----
        // B : --|******|---------
        return [$intervalB[1], $intervalA[1]];
    } elseif ( overlapRight($intervalB, $intervalA )) {
        // A : ------|******|----------
        // B : ----------|******|------
        return [$intervalA[0], $intervalB[0]];
    } else {
        throw new Exception('bam');
    }
}
?>
<pre>
<?php
// tests
$int1 = [1,4];
$int2 = [6,9];
$int3 = [3,7];
$int4 = [-1,7];
$int5 = [-1,3];
$int6 = [2,3];

echo "int1 - int1 = " . VarDumper::dumpAsString(substract($int1, $int1)). "\n";
echo "int1 - int2 = " . VarDumper::dumpAsString(substract($int1, $int2)). "\n"; // no overlap
echo "int1 - int3 = " . VarDumper::dumpAsString(substract($int1, $int3)). "\n"; // overlap Right
echo "int1 - int4 = " . VarDumper::dumpAsString(substract($int1, $int4)). "\n"; // include
echo "int1 - int5 = " . VarDumper::dumpAsString(substract($int1, $int5)). "\n"; // left overlap
echo "int1 - int6 = " . VarDumper::dumpAsString(substract($int1, $int6)). "\n"; // contains



echo "------";
echo "int1 + int2 = " . VarDumper::dumpAsString(add($int1, $int2)). "\n";
//echo "int2 + int1 = " . VarDumper::dumpAsString(add($int2, $int1)). "\n";
echo "int1 + int3 = " . VarDumper::dumpAsString(add($int1, $int3)). "\n";
echo "int1 + int4 = " . VarDumper::dumpAsString(add($int1, $int4)). "\n";
echo "int1 + int5 = " . VarDumper::dumpAsString(add($int1, $int5)). "\n";


?>
</pre>
<hr/>
<pre>
<?php

echo \yii\helpers\VarDumper::dumpAsString($byProduct);
?>
</pre>
