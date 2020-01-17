<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-salesorder-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#salesorderform-sotransdate-disp').prop('disabled', true);
    $('#salesorderform-plantid').prop('disabled', true);
    $('#salesorderform-headernote').prop('disabled', true);
    $('.ms-salesorder-view :input').prop('disabled', true);
    $('.ms-salesorder-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);