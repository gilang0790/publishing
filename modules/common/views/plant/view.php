<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

?>
<div class="ms-plant-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#plant-plantcode').prop('disabled', true);
    $('#plant-companyid').prop('disabled', true);
    $('#plant-description').prop('disabled', true);
    $('.ms-plant-view :input').prop('disabled', true);
    $('.ms-plant-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);