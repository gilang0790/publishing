<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

?>
<div class="ms-customer-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#customer-fullname').prop('disabled', true);
    $('#customer-pic').prop('disabled', true);
    $('#customer-phoneno').prop('disabled', true);
    $('#customer-cityid').prop('disabled', true);
    $('#customer-address').prop('disabled', true);
    $('#customer-email').prop('disabled', true);
    $('#customer-bankname').prop('disabled', true);
    $('#customer-bankaccountno').prop('disabled', true);
    $('.ms-customer-view :input').prop('disabled', true);
    $('.ms-customer-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);