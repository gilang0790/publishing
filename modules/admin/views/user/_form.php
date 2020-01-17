<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\admin\models\Menuaccess;
?>

<div class="box box-primary">
    <?php
    $form = ActiveForm::begin(['id' => 'theForm', 'enableAjaxValidation' => true]); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-10">
                    <h3><?= Menuaccess::getFormName($this->title, Yii::$app->controller->action->id) ?></h3>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Informasi <?= $this->title ?></h4></div>
                <div class="panel-body">
                    <div class='row'>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'username')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'fullName')->textInput([
                                'maxlength' => true
                            ])
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'email')->textInput([
                                'maxlength' => true
                            ])
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php if ($model->scenario != 'update' && !isset($isView)) : ?>
                            <?= $form->field($model, 'password_input')->passwordInput(['maxlength' => true, 'placeholder'=> ''])
                                ->hint('Kata sandi harus terdiri dari huruf kapital, huruf kecil, angka, dan salah satu simbol:! @ # $% ^ &? * ()') ?>
                            <?php endif; ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?php if (!isset($isView)): ?>
        <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Simpan', 
        ['class' => 'btn btn-primary btnSave']) ?>
        <?php endif; ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Batal', ['index'], ['class'=>'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>