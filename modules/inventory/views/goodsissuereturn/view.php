<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-goodsissuereturn-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#goodsissuereturnreturnform-girtransdate-disp').prop('disabled', true);
    $('#goodsissuereturnreturnform-slocid').prop('disabled', true);
    $('#goodsissuereturnreturnform-headernote').prop('disabled', true);
    $('.ms-goodsissuereturn-view :input').prop('disabled', true);
    $('.ms-goodsissuereturn-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);