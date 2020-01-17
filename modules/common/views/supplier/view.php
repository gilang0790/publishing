<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-supplier-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#supplier-fullname').prop('disabled', true);
    $('#supplier-pic').prop('disabled', true);
    $('#supplier-phoneno').prop('disabled', true);
    $('#supplier-cityid').prop('disabled', true);
    $('#supplier-address').prop('disabled', true);
    $('#supplier-email').prop('disabled', true);
    $('#supplier-bankname').prop('disabled', true);
    $('#supplier-bankaccountno').prop('disabled', true);
    $('.ms-supplier-view :input').prop('disabled', true);
    $('.ms-supplier-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);