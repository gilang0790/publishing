<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\admin\models\search\ModulesSearchModel;
use app\modules\admin\models\search\MenuaccessSearchModel;
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
                            $form->field($model, 'menuname')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'description')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'menuurl')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'menuicon')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'parentid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(MenuaccessSearchModel::findActive()->orderBy('menuaccessid')->all(),
                                        'menuaccessid', 'description'),
                                'options' => [
                                    'prompt' => '--- Pilih Menu ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'moduleid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(ModulesSearchModel::findActive()->orderBy('moduleid')->all(),
                                        'moduleid', 'moduledesc'),
                                'options' => [
                                    'prompt' => '--- Pilih Modul ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'sortorder')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
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