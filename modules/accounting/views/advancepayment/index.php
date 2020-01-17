<?php

use app\components\AppHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\admin\models\Menuaccess;


$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advancepayment-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'panel' => [
            'heading' => Menuaccess::gridTitle($this->title),
        ],
        'toolbar' => [
            [
                'content' =>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], [
                    'class' => 'btn btn-default',
                    'title' => 'Reset'
                    ]) .
                    AppHelper::getToolbarCreateButton("Tambah")
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'width' => '4%'
            ],
            [
                'attribute' => 'umtransnum',
                'width' => '15%',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'umtransdate',
                'format' => ['date', 'php:d-m-Y'],
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => AppHelper::getDatePickerRangeConfig(),
                'filterInputOptions' => [
                    'class' => 'text-center form-control'
                ],
                'hAlign' => 'center',
                'width' => '16%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'plantid',
                'value' => 'plant.plantcode',
                'width' => '10%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'salesorderid',
                'value' => 'salesorder.sotransnum',
                'width' => '15%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'label' => 'Total Pesanan',
                'value' => 'salesorder.grandtotal',
                'width' => '10%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'format' => ['decimal', 0]
            ],
            [
                'attribute' => 'amount',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-right'
                ],
                'format' => ['decimal', 0]
            ],
            AppHelper::getDataWfStatus(),
            AppHelper::getActionGrid(['view', 'update', 'approval', 'reject'])
        ],
    ]); ?>
    
</div>