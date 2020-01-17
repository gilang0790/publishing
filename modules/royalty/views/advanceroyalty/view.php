<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-advanceroyalty-view">
    <?= $this->render('_view', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#advanceroyaltyform-umrtransdate-disp').prop('disabled', true);
    $('#advanceroyaltyform-plantid').prop('disabled', true);
    $('#advanceroyaltyform-headernote').prop('disabled', true);
    $('.ms-advanceroyalty-view :input').prop('disabled', true);
    $('.ms-advanceroyalty-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);