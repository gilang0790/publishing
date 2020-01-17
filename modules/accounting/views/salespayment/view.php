<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-salespayment-view">
    <?= $this->render('_view', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#salespaymentform-artransdate-disp').prop('disabled', true);
    $('#salespaymentform-plantid').prop('disabled', true);
    $('#salespaymentform-headernote').prop('disabled', true);
    $('.ms-salespayment-view :input').prop('disabled', true);
    $('.ms-salespayment-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);