<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Groupaccess */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="menuauth-create">
<?= $this->render('_form', [
        'model' => $model,
        'isChange' => false,
    ]) ?>
</div>
