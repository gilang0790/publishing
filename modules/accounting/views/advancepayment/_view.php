<?php

use app\components\AppHelper;
use app\modules\admin\models\Menuaccess;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\order\models\search\SalesorderBrowseModel;
use kartik\datecontrol\DateControl;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'validateOnSubmit' => false,
        'options' => [
            'id' => 'form-advance-payment'
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
                        <div class="col-md-3">
                            <?=
                                $form->field($model, 'umtransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'umtransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <?= $form->field($model, 'salesorderid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(SalesorderBrowseModel::find()->orderBy('id')->all(),
                                        'id', 'sotransnum'),
                                'options' => [
                                    'prompt' => '--- Pilih Pesanan Penjualan ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'receiptno')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'bankname')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'bankaccountno')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'amount')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'headernote')->textarea(['class' => 'form-control']) ?>
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