<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\royalty\models\Royaltypayment */

$this->title = 'Create Royaltypayment';
$this->params['breadcrumbs'][] = ['label' => 'Royaltypayments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="royaltypayment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
