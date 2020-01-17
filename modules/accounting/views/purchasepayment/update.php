<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="purchasepayment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'isUpdate' => true
    ]) ?>

</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#purchasepaymentform-pptransnum-disp').prop('disabled', true);
    $('#purchasepaymentform-pptransdate-disp').prop('disabled', true);
    $('#purchasepaymentform-plantid').prop('disabled', true);
    $('#purchasepaymentform-invoiceapid').prop('disabled', true);
    $('#purchasepaymentform-addressbookid').prop('disabled', true);
    $('#purchasepaymentform-apamount').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);