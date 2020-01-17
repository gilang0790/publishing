<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\inventory\models\Stockcard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-header with-border">
        <h3 class="box-title">
            Stockcard Information
        </h3>
    </div>
    <div class="box-body">
    
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'stockid')->textInput() ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'productid')->textInput() ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'unitofmeasureid')->textInput() ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'slocid')->textInput() ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'storagebinid')->textInput() ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'transdate')->textInput() ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'refnum')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'qtyin')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'qtyout')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'transtype')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'hpp')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'buyprice')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'createdAt')->textInput() ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'updatedAt')->textInput() ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'createdBy')->textInput() ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'updatedBy')->textInput() ?>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-danger', 'style' => 'width: 100px;']) ?>    
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
