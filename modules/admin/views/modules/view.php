<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-module-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#modules-modulename').prop('disabled', true);
    $('#modules-moduledesc').prop('disabled', true);
    $('#modules-moduleicon').prop('disabled', true);
    $('#modules-isinstall').prop('disabled', true);
    $('.ms-module-view :input').prop('disabled', true);
    $('.ms-module-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);