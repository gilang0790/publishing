<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\components\AppHelper;
use app\modules\common\models\search\SlocSearchModel;
use app\modules\admin\models\Menuaccess;

?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'validateOnSubmit' => false,
        'options' => [
            'id' => 'form-goods-issue'
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
                                $form->field($model, 'gitransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'gitransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'slocid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(SlocSearchModel::dropdownList()->orderBy('slocid')->all(),
                                        'slocid', 'sloccode'),
                                'options' => [
                                    'prompt' => '--- Pilih Gudang ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'shippingname')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'shippingcost')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'address')->textarea(['class' => 'form-control']) ?>
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
                                'attribute' => 'storagebinid',
                                'value' => 'storagebin.description',
                                'width' => '15%',
                                'headerOptions' => [
                                    'class' => 'text-center'
                                ],
                                'contentOptions' => [
                                    'class' => 'text-left'
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Ringkasan Transaksi</h4></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'headernote')->textArea(['row' => 6]) ?>
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