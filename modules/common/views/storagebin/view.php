<?php

/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = 'Rak - Detail';
$this->params['breadcrumbs'][] = ['label' => 'Rak', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Detail';

?>
<div class="ms-storagebin-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#storagebin-slocid').prop('disabled', true);
    $('#storagebin-description').prop('disabled', true);
    $('#storagebin-ismultiproduct').prop('disabled', true);
    $('.ms-storagebin-view :input').prop('disabled', true);
    $('.ms-storagebin-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);