<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-goodsissue-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#goodsissueform-gitransdate-disp').prop('disabled', true);
    $('#goodsissueform-slocid').prop('disabled', true);
    $('#goodsissueform-headernote').prop('disabled', true);
    $('.ms-goodsissue-view :input').prop('disabled', true);
    $('.ms-goodsissue-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);