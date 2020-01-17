<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-advancepayment-view">
    <?= $this->render('_view', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#advancepaymentform-umtransdate-disp').prop('disabled', true);
    $('#advancepaymentform-plantid').prop('disabled', true);
    $('#advancepaymentform-headernote').prop('disabled', true);
    $('.ms-advancepayment-view :input').prop('disabled', true);
    $('.ms-advancepayment-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);