<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\components\AppHelper;
use app\modules\common\models\search\CustomerSearchModel;
use app\modules\inventory\models\search\GoodsissueBrowseModel;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\admin\models\Menuaccess;

?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'validateOnSubmit' => false,
        'options' => [
            'id' => 'form-invoicear'
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
                                $form->field($model, 'artransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'artransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-2">
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
                        <div class="col-md-4">
                            <?= $form->field($model, 'goodsissueid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(GoodsissueBrowseModel::findActive()->orderBy('id')->all(),
                                        'id', 'gitransnum'),
                                'options' => [
                                    'prompt' => '--- Pilih Pengeluaran Barang ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'addressbookid')->widget(Select2::classname(), [
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
                        <div class="col-md-2">
                            <?= $form->field($model, 'grandtotal')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'shippingcost')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'aramount')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'payamount')->textInput(['class' => 'form-control input-decimal text-right']) ?>
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
                                'width' => '5%',
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
                                'attribute' => 'vat',
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
                                'attribute' => 'discount',
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
        </div>
    </div>
    <div class="box-footer text-right">
        <?= AppHelper::getPrintButton($model->id) ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>