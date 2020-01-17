<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-goodsreceiptreturn-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#goodsreceiptreturnreturnform-grrtransdate-disp').prop('disabled', true);
    $('#goodsreceiptreturnreturnform-slocid').prop('disabled', true);
    $('#goodsreceiptreturnreturnform-headernote').prop('disabled', true);
    $('.ms-goodsreceiptreturn-view :input').prop('disabled', true);
    $('.ms-goodsreceiptreturn-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);