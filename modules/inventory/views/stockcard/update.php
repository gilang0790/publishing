<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\inventory\models\Stockcard */

$this->title = 'Update Stockcard: ' . $model->stockcardid;
$this->params['breadcrumbs'][] = ['label' => 'Stockcards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->stockcardid, 'url' => ['view', 'id' => $model->stockcardid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="stockcard-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
