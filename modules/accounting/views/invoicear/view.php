<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-invoicear-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#invoicearform-artransdate-disp').prop('disabled', true);
    $('#invoicearform-plantid').prop('disabled', true);
    $('#invoicearform-headernote').prop('disabled', true);
    $('.ms-invoicear-view :input').prop('disabled', true);
    $('.ms-invoicear-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);