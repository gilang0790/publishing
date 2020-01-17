<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-menuauth-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#menuauth-menuauthid').prop('disabled', true);
    $('#menuauth-menuobject').prop('disabled', true);
    $('.ms-menuauth-view :input').prop('disabled', true);
    $('.ms-menuauth-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);