<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-purchaseorder-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#purchaseorderform-potransdate-disp').prop('disabled', true);
    $('#purchaseorderform-plantid').prop('disabled', true);
    $('#purchaseorderform-headernote').prop('disabled', true);
    $('.ms-purchaseorder-view :input').prop('disabled', true);
    $('.ms-purchaseorder-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);