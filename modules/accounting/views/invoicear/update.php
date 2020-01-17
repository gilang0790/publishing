<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="invoicear-update">

    <?= $this->render('_form', [
        'model' => $model,
        'details' => $details,
        'isUpdate' => true
    ]) ?>

</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#invoicearform-artransnum-disp').prop('disabled', true);
    $('#invoicearform-artransdate-disp').prop('disabled', true);
    $('#invoicearform-plantid').prop('disabled', true);
    $('#invoicearform-goodsissueid').prop('disabled', true);
    $('#invoicearform-addressbookid').prop('disabled', true);
    $('#invoicearform-aramount').prop('disabled', true);
    $('#invoicearform-payamount').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);