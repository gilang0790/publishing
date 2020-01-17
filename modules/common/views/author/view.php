<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

?>
<div class="ms-author-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#author-fullname').prop('disabled', true);
    $('#author-pic').prop('disabled', true);
    $('#author-phoneno').prop('disabled', true);
    $('#author-cityid').prop('disabled', true);
    $('#author-address').prop('disabled', true);
    $('#author-email').prop('disabled', true);
    $('#author-bankname').prop('disabled', true);
    $('#author-bankaccountno').prop('disabled', true);
    $('.ms-author-view :input').prop('disabled', true);
    $('.ms-author-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);