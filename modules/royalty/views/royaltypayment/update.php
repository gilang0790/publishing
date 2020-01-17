<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="royaltypayment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'isUpdate' => true
    ]) ?>

</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#royaltypaymentform-rptransnum-disp').prop('disabled', true);
    $('#royaltypaymentform-rptransdate-disp').prop('disabled', true);
    $('#royaltypaymentform-plantid').prop('disabled', true);
    $('#royaltypaymentform-invoiceroyaltyid').prop('disabled', true);
    $('#royaltypaymentform-addressbookid').prop('disabled', true);
    $('#royaltypaymentform-invoiceamount').prop('disabled', true);
    $('#royaltypaymentform-paidamount').prop('disabled', true);
    $('#royaltypaymentform-advanceamount').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);