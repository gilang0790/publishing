<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\City */

$this->title = 'Update City: ' . $model->cityid;
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cityid, 'url' => ['view', 'id' => $model->cityid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="city-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
