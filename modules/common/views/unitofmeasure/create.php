<?php

use app\modules\admin\models\Menuaccess;


/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Unitofmeasure */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="sloc-create">
    <?= $this->render('_form', [
        'model' => $model,
        'isChange' => false,
    ]) ?>
</div>
