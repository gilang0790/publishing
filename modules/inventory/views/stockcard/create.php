<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\inventory\models\Stockcard */

$this->title = 'Create Stockcard';
$this->params['breadcrumbs'][] = ['label' => 'Stockcards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stockcard-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
