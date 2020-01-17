<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-sloc-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#sloc-sloccode').prop('disabled', true);
    $('#sloc-plantid').prop('disabled', true);
    $('#sloc-description').prop('disabled', true);
    $('.ms-sloc-view :input').prop('disabled', true);
    $('.ms-sloc-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);