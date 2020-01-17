<?php

use \yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\modules\common\models\search\CategorySearchModel;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\common\models\search\SlocSearchModel;
?>

<div class="stock-card-search">
    <?php
    $form = ActiveForm::begin([
                'id' => 'stock-period-form',
                'enableAjaxValidation' => false,
                'method' => 'GET',
                'options' => [
                    'data-pjax' => true,
                    'name' => 'stock-card-search-form',
                ],
    ]);
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4">
                   <?=
                    $form->field($model, 'plantid')->widget(Select2::className(), [
                        'data' =>  ArrayHelper::map(PlantSearchModel::dropdownList()->all(), 'plantid', 'plantcode'),
                        'options' => [
                            'placeholder' => '- Pilih Cabang -',
                            'multiple' => true
                        ],
                        'size' => Select2::MEDIUM,
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
                    ?>
                </div>
                <div class="col-md-4">
                   <?=
                    $form->field($model, 'slocid')->widget(Select2::className(), [
                        'data' =>  ArrayHelper::map(SlocSearchModel::dropdownList()->all(), 'slocid', 'sloccode'),
                        'options' => [
                            'placeholder' => '- Pilih Gudang -',
                            'multiple' => true
                        ],
                        'size' => Select2::MEDIUM,
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
                    ?>
                </div>
                <div class="col-md-4">
                    <?=
                     $form->field($model, 'categoryid')->widget(Select2::className(), [
                         'data' =>  ArrayHelper::map(CategorySearchModel::findActive()->all(), 'categoryid', 'categoryname'),
                         'options' => [
                             'placeholder' => '- Pilih Kategori -',
                         ],
                         'size' => Select2::MEDIUM,
                         'pluginOptions' => [
                             'allowClear' => true
                         ],
                     ])
                     ?>
                 </div>
            </div>
            <div class="row">
                 <div class="col-md-4">
                     <?= $form->field($model, 'productname')->textInput(['id' => 'productNameHead']) ?> 
                 </div>
                 <div class="col-md-4">
                     <?= $form->field($model, 'productcode')->textInput(['id' => 'procutCodeHead']) ?> 
                 </div>
            </div>
        </div>
    </div>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-search with-text"></i>Cari', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-refresh with-text"></i>Hapus Filter', ['index'], ['class' => 'btn btn-primary', 'title' => Yii::t('app', 'Clear  Filter')]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
