<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="salespayment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'isUpdate' => true
    ]) ?>

</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#salespaymentform-sptransnum-disp').prop('disabled', true);
    $('#salespaymentform-sptransdate-disp').prop('disabled', true);
    $('#salespaymentform-plantid').prop('disabled', true);
    $('#salespaymentform-invoicearid').prop('disabled', true);
    $('#salespaymentform-addressbookid').prop('disabled', true);
    $('#salespaymentform-aramount').prop('disabled', true);
    $('#salespaymentform-paidamount').prop('disabled', true);
    $('#salespaymentform-advanceamount').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);