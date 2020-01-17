<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\common\models\search\AuthorSearchModel;
use app\modules\admin\models\Menuaccess;
use app\modules\common\models\search\ProductSearchModel;
use kartik\select2\Select2;
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
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Informasi <?= $this->title ?></h4></div>
            <div class="panel-body">
                <div class='row'>
                    <div class='col-md-12'>
                        <?=
                        $form->field($model, 'addressbookid')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(AuthorSearchModel::findActive()->orderBy('fullname')->all(),
                                    'addressbookid', 'fullname'),
                            'options' => [
                                'prompt' => '--- Pilih Penulis ---'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <?=
                        $form->field($model, 'productid')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(ProductSearchModel::findActive()->orderBy('productname')->all(),
                                    'productid', 'productname'),
                            'options' => [
                                'prompt' => '--- Pilih Buku ---'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <div class='row'>
                    <div class="col-md-4">
                        <?= $form->field($model, 'period')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'fee')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'tax')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <?= $form->field($model, 'notes')->textArea(['row' => 10]) ?>
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
