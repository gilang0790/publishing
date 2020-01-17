<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="invoiceap-update">

    <?= $this->render('_form', [
        'model' => $model,
        'details' => $details,
        'isUpdate' => true
    ]) ?>

</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#invoicearform-aptransnum-disp').prop('disabled', true);
    $('#invoicearform-aptransdate-disp').prop('disabled', true);
    $('#invoicearform-plantid').prop('disabled', true);
    $('#invoicearform-goodsreceiptid').prop('disabled', true);
    $('#invoicearform-addressbookid').prop('disabled', true);
    $('#invoicearform-apamount').prop('disabled', true);
    $('#invoicearform-payamount').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);