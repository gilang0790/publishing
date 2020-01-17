<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\User */

$this->title = 'Grup dan Menu - Tambah';
$this->params['breadcrumbs'][] = ['label' => 'Group dan Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Tambah';
?>
<div class="groupmenu-create">
    <?= $this->render('_form', [
        'model' => $model,
        'isChange' => false,
    ]) ?>
</div>
