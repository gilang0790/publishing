<?php

use app\modules\admin\models\Menuaccess;


$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="goodsreceipt-update">
    <?= $this->render('_form', [
        'model' => $model,
        'details' => $details,
        'isUpdate' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#goodsreceiptform-grtransdate-disp').prop('readonly', true);
    $('.detailProductID').prop('disabled', true);
    $('.add-item').prop('disabled', true);
    $('.remove-item').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);