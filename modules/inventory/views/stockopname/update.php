<?php

use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $model app\modules\inventory\models\Stockopname */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="stockopname-update">

    <?= $this->render('_form', [
        'model' => $model,
        'details' => $details,
        'isUpdate' => true
    ]) ?>

</div>
