<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\common\models\search\CustomerSearchModel;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\admin\models\Menuaccess;
use wbraganca\dynamicform\DynamicFormWidget;

?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'validateOnSubmit' => false,
        'options' => [
            'id' => 'form-sales-order-info'
        ],
    ]); ?>
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
                <div class="panel-heading"><h4>Informasi Transaksi</h4></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'plantid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PlantSearchModel::dropdownList()->orderBy('plantid')->all(),
                                        'plantid', 'plantcode'),
                                'options' => [
                                    'prompt' => '--- Pilih Cabang ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                            <?= Html::activeHiddenInput($model, 'plantid'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Detail Transaksi</h4></div>
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper',  // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items',          // required: css class selector
                        'widgetItem' => '.item',                     // required: css class
                        'limit' => 999,                                // the maximum times, an element can be cloned (default 999)
                        'min' => 1,                                  // 0 or 1 (default 1)
                        'insertButton' => '.add-item',               // css class
                        'deleteButton' => '.remove-item',            // css class
                        'model' => $details[0],
                        'formId' => 'form-sales-order-info',
                        'formFields' => [
                            'head_id',
                            'productid',
                            'qty',
                            'price',
                            'vat',
                            'discount',
                            'total'
                        ],
                    ]); ?>
                    
                    <div class="container-items">
                        <?php foreach ($details as $i => $detail): ?>
                        <div class="item row">
                            <?php
                                // necessary for update action.
                                if (!$detail->isNewRecord) {
                                    echo Html::activeHiddenInput($detail, "[{$i}]id");
                                }
                            ?>
                            <div class="col-sm-8 col-md-4">
                                <?= $form->field($detail, "[{$i}]productid")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map(ProductSearchModel::findProductSales($model->salesorderid)->orderBy('productid')->all(),
                                            'productid', 'productname'),
                                    'options' => [
                                        'prompt' => '--- Pilih Barang ---'
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]); ?>
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <?= $form->field($detail, "[{$i}]qty")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control input-decimal text-right salesdetailqty']) ?>
                            </div>
                            <div class="col-sm-8 col-md-4">
                                <?= $form->field($detail, "[{$i}]addressbookid")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map(CustomerSearchModel::findActive()->orderBy('addressbookid')->all(),
                                            'addressbookid', 'fullname'),
                                    'options' => [
                                        'prompt' => '--- Pilih Pelanggan ---'
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]); ?>
                            </div>
                            <div class="col-sm-3 col-md-1 item-action">
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs">
                                        <i class="glyphicon glyphicon-plus"></i></button> 
                                    <button type="button" class="remove-item btn btn-danger btn-xs">
                                        <i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php DynamicFormWidget::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?php if (!isset($isView)): ?>
        <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Simpan', 
            ['class' => 'btn btn-primary btnSave']) ?>
        <?php endif; ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>