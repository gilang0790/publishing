<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\components\AppHelper;
use app\modules\common\models\search\PaymentmethodSearchModel;
use app\modules\common\models\search\SupplierSearchModel;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $model app\modules\inventory\models\Salesorder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'validateOnSubmit' => false,
        'options' => [
            'id' => 'form-purchase-order'
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
                        <div class="col-md-4">
                            <?=
                                $form->field($model, 'potransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'potransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'plantid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PlantSearchModel::dropdownList()->orderBy('plantid')->all(),
                                        'plantid', 'plantcode'),
                                'options' => [
                                    'prompt' => '--- Pilih Cabang ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'addressbookid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(SupplierSearchModel::findActive()->orderBy('addressbookid')->all(),
                                        'addressbookid', 'fullname'),
                                'options' => [
                                    'prompt' => '--- Pilih Pemasok ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'paymentmethodid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PaymentmethodSearchModel::findActive()->orderBy('paymentmethodid')->all(),
                                        'paymentmethodid', 'paymentname'),
                                'options' => [
                                    'prompt' => '--- Pilih Pembayaran ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'billto')->textarea(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'shipto')->textarea(['class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Detail Transaksi</h4></div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            [
                                'class' => 'kartik\grid\SerialColumn',
                                'width' => '4%'
                            ],
                            [
                                'attribute' => 'productid',
                                'value' => 'product.productname',
                                'width' => '15%',
                                'headerOptions' => [
                                    'class' => 'text-center'
                                ],
                                'contentOptions' => [
                                    'class' => 'text-left'
                                ],
                            ],
                            [
                                'attribute' => 'qty',
                                'width' => '15%',
                                'headerOptions' => [
                                    'class' => 'text-center'
                                ],
                                'contentOptions' => [
                                    'class' => 'text-right'
                                ],
                                'format' => ['decimal', 0]
                            ],
                            [
                                'attribute' => 'price',
                                'width' => '15%',
                                'headerOptions' => [
                                    'class' => 'text-center'
                                ],
                                'contentOptions' => [
                                    'class' => 'text-right'
                                ],
                                'format' => ['decimal', 0]
                            ],
                            [
                                'attribute' => 'total',
                                'width' => '15%',
                                'headerOptions' => [
                                    'class' => 'text-center'
                                ],
                                'contentOptions' => [
                                    'class' => 'text-right'
                                ],
                                'format' => ['decimal', 0]
                            ]
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Ringkasan Transaksi</h4></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <?= $form->field($model, 'headernote')->textArea(['row' => 6]) ?>
                        </div>
                        <div class="col-md-4">
                            <?=
                                $form->field($model, 'grandtotal')
                                ->textInput([
                                    'readonly' => true,
                                    'id' => 'salesordertotal',
                                    'class' => 'form-control input-decimal text-right',
                                    'style' => 'font-size: 18px',
                                ])
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?= AppHelper::getPrintButton($model->id) ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>