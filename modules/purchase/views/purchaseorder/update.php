<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="purchaseorder-update">

    <?= $this->render('_form', [
        'model' => $model,
        'details' => $details,
        'isUpdate' => true
    ]) ?>

</div>
