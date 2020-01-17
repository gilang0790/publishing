<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-royaltysetting-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#royaltysetting-addressbookid').prop('disabled', true);
    $('#royaltysetting-productid').prop('disabled', true);
    $('#royaltysetting-fee').prop('disabled', true);
    $('#royaltysetting-tax').prop('disabled', true);
    $('#royaltysetting-period').prop('disabled', true);
    $('#royaltysetting-notes').prop('disabled', true);
    $('.ms-royaltysetting-view :input').prop('disabled', true);
    $('.ms-royaltysetting-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);