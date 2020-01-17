<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-royaltypayment-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#royaltypaymentform-rptransdate-disp').prop('disabled', true);
    $('#royaltypaymentform-plantid').prop('disabled', true);
    $('#royaltypaymentform-headernote').prop('disabled', true);
    $('.ms-royaltypayment-view :input').prop('disabled', true);
    $('.ms-royaltypayment-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);