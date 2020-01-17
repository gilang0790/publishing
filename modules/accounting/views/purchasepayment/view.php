<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-purchasepayment-view">
    <?= $this->render('_view', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#purchasepaymentform-pptransdate-disp').prop('disabled', true);
    $('#purchasepaymentform-plantid').prop('disabled', true);
    $('#purchasepaymentform-headernote').prop('disabled', true);
    $('.ms-purchasepayment-view :input').prop('disabled', true);
    $('.ms-purchasepayment-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);