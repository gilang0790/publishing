<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

?>
<div class="ms-groupmenuauth-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#groupmenuauth-menuauthid').prop('disabled', true);
    $('#groupmenuauth-groupaccess').prop('disabled', true);
    $('#groupmenuauth-menuvalueid').prop('disabled', true);
    $('.ms-groupmenuauth-view :input').prop('disabled', true);
    $('.ms-groupmenuauth-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);