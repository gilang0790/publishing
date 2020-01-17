<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\admin\models\search\CitySearchModel;
use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Author */
/* @var $form yii\widgets\ActiveForm */
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
                            <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class='col-md-4'>
                            <?= $form->field($model, 'pic')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class='col-md-4'>
                            <?= $form->field($model, 'phoneno')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>
                            <?= $form->field($model, 'cityid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(CitySearchModel::findActive()->orderBy('cityname')->all(),
                                        'cityid', 'cityname'),
                                'options' => [
                                    'prompt' => '--- City ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                        <div class='col-md-4'>
                            <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'row' => 6]) ?>
                        </div>
                        <div class='col-md-4'>
                            <?= $form->field($model, 'email')->textInput() ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>
                            <?= $form->field($model, 'bankname')->textInput() ?>
                        </div>
                        <div class='col-md-4'>
                            <?= $form->field($model, 'bankaccountno')->textInput() ?>
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