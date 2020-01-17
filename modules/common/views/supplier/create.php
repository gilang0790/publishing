<?php

use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Supplier */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="supplier-create">
    <?= $this->render('_form', [
        'model' => $model,
        'isChange' => false,
    ]) ?>
</div>