<?php

use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $model app\modules\inventory\models\Goodsissue */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="goodsissue-update">
    <?= $this->render('_form', [
        'model' => $model,
        'details' => $details,
        'isUpdate' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#goodsissueform-gitransdate-disp').prop('readonly', true);
    $('.detailProductID').prop('disabled', true);
    $('.add-item').prop('disabled', true);
    $('.remove-item').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);