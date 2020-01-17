<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\admin\models\search\MenuauthSearchModel;
use app\modules\admin\models\search\GroupaccessSearchModel;
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
                        <div class='col-md-4'>
                            <?=
                            $form->field($model, 'groupaccessid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(GroupaccessSearchModel::findActive()->orderBy('groupaccessid DESC')->all(),
                                        'groupaccessid', 'groupname'),
                                'options' => [
                                    'prompt' => '--- Pilih Grup ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class='col-md-4'>
                            <?=
                            $form->field($model, 'menuauthid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(MenuauthSearchModel::findActive()->orderBy('menuauthid')->all(),
                                        'menuauthid', 'menuobject'),
                                'options' => [
                                    'prompt' => '--- Pilih User ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class='col-md-4'>
                            <?=
                            $form->field($model, 'menuvalueid')->textInput([
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