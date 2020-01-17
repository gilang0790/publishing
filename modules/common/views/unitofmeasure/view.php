<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

?>
<div class="ms-unitofmeasure-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#unitofmeasure-uomcode').prop('disabled', true);
    $('#unitofmeasure-description').prop('disabled', true);
    $('.unitofmeasure-view :input').prop('disabled', true);
    $('.unitofmeasure-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);