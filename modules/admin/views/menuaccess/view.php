<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-group-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#menuaccess-menuaccessid').prop('disabled', true);
    $('#menuaccess-menuname').prop('disabled', true);
    $('.ms-group-view :input').prop('disabled', true);
    $('.ms-group-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);