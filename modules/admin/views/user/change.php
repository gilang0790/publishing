<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="ms-user-view">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#user-username').prop('disabled', true);
    $('#user-fullname').prop('disabled', true);
    $('#user-email').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);