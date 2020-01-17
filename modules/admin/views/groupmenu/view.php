<?php

/* @var $this yii\web\View */

$this->title = 'Grup dan Menu - Detail';
$this->params['breadcrumbs'][] = ['label' => 'Grup', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Detail';

?>
<div class="ms-groupmenu-view">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
</div>