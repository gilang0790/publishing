<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\City */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-header with-border">
        <h3 class="box-title">
            City Information
        </h3>
    </div>
    <div class="box-body">
    
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'provinceid')->textInput() ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'citycode')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'cityname')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'status')->textInput() ?>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-danger', 'style' => 'width: 100px;']) ?>    
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
