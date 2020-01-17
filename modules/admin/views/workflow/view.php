<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-workflow-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#workflow-wfname').prop('disabled', true);
    $('#workflow-wfdesc').prop('disabled', true);
    $('#workflow-wfminstat').prop('disabled', true);
    $('#workflow-wfmaxstat').prop('disabled', true);
    $('.ms-workflow-view :input').prop('disabled', true);
    $('.ms-workflow-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);