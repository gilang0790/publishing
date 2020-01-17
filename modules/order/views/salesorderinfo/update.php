<?php

use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="salesorder-update">

    <?= $this->render('_form', [
        'model' => $model,
        'details' => $details,
        'isUpdate' => true
    ]) ?>

</div>

<?php
$js = <<< SCRIPT
$(document).ready(function(){
    $('#salesorderinfoform-plantid').prop('disabled', true);
});
SCRIPT;
$this->registerJs($js);