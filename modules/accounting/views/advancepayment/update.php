<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="advancepayment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'isUpdate' => true
    ]) ?>

</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#advancepaymentform-umtransnum-disp').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);