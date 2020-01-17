<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-product-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#product-productcode').prop('disabled', true);
    $('#product-categoryid').prop('disabled', true);
    $('#product-unitofmeasureid').prop('disabled', true);
    $('#product-productname').prop('disabled', true);
    $('#product-isbn').prop('disabled', true);
    $('#product-author').prop('disabled', true);
    $('#product-weight').prop('disabled', true);
    $('#product-size').prop('disabled', true);
    $('#product-notes').prop('disabled', true);
    $('.ms-product-view :input').prop('disabled', true);
    $('.ms-product-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);