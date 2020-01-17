<?php

use app\modules\admin\models\Menuaccess;
/* @var $this yii\web\View */
/* @var $model app\models\TrTaskhead */

$this->title = 'User dan Grup - Detail';
$this->params['breadcrumbs'][] = ['label' => 'User dan Grup', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Detail';

?>
<div class="ms-usergroup-view">
    <?= $this->render('_form', [
        'model' => $model,
        'isView' => true
    ]) ?>
</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#usergroup-userID').prop('disabled', true);
    $('#usergroup-groupaccess').prop('disabled', true);
    $('.ms-usergroup-view :input').prop('disabled', true);
    $('.ms-usergroup-view select').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);