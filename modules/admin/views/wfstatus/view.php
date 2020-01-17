<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-wfstatus-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#wfstatus-workflowid').prop('disabled', true);
    $('#wfstatus-wfstat').prop('disabled', true);
    $('#wfstatus-wfstatusname').prop('disabled', true);
    $('.ms-wfstatus-view :input').prop('disabled', true);
    $('.ms-wfstatus-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);