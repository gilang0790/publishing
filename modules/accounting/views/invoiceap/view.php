<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-invoiceap-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#invoiceapform-aptransdate-disp').prop('disabled', true);
    $('#invoiceapform-plantid').prop('disabled', true);
    $('#invoiceapform-headernote').prop('disabled', true);
    $('.ms-invoiceap-view :input').prop('disabled', true);
    $('.ms-invoiceap-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);