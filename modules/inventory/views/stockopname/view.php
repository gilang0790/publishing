<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-stockopname-view">
    <?= $this->render('_view', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#stockopnameform-bstransdate-disp').prop('disabled', true);
    $('#stockopnameform-slocid').prop('disabled', true);
    $('#stockopnameform-headernote').prop('disabled', true);
    $('.ms-stockopname-view :input').prop('disabled', true);
    $('.ms-stockopname-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);