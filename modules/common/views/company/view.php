<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-company-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#company-address').prop('disabled', true);
    $('#company-cityid').prop('disabled', true);
    $('#company-zipcode').prop('disabled', true);
    $('#company-companyname').prop('disabled', true);
    $('#company-companycode').prop('disabled', true);
    $('#company-phoneno').prop('disabled', true);
    $('#company-email').prop('disabled', true);
    $('#company-webaddress').prop('disabled', true);
    $('.ms-company-view :input').prop('disabled', true);
    $('.ms-company-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);