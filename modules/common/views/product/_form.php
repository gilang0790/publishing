<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\common\models\Product;
use app\modules\common\models\search\CategorySearchModel;
use app\modules\common\models\search\UomSearchModel;
use app\modules\admin\models\Menuaccess;
?>

<div class="box box-primary">
    <?php
    $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>
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
                            $form->field($model, 'productname')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'productcode')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-3'>
                            <?=
                            $form->field($model, 'unitofmeasureid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(UomSearchModel::findActive()->orderBy('uomcode')->all(),
                                        'unitofmeasureid', 'uomcode'),
                                'options' => [
                                    'prompt' => '--- Pilih Satuan ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class='col-md-3'>
                            <?=
                            $form->field($model, 'type')->widget(Select2::classname(), [
                                'data' => Product::$type_array,
                                'options' => [
                                    'prompt' => '--- Pilih Tipe ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'categoryid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(CategorySearchModel::findActive()->orderBy('categoryname')->all(),
                                        'categoryid', 'categoryname'),
                                'options' => [
                                    'prompt' => '--- Pilih Kategori ---'
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
                            $form->field($model, 'isbn')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'author')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'weight')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                        <div class='col-md-6'>
                            <?=
                            $form->field($model, 'size')->textInput([
                                'maxlength' => true,
                            ])
                            ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-12'>
                            <?=
                            $form->field($model, 'notes')->textArea([
                                'row' => 10
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