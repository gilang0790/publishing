<?php

use app\components\AppHelper;
use app\modules\admin\models\Menuaccess;
use app\modules\order\models\Salesorder;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salesorder-index">
    <div class="box box-solid box-info">
        <div class="box-header">
            <h3 class="box-title">INFORMASI MENU</h3>
        </div>
        <div class="box-body">
            <?= Salesorder::FEATURE_DESCRIPTION ?>
        </div>
    </div>
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
                    AppHelper::getToolbarCreateButton('Tambah')
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'width' => '4%'
            ],
            [
                'attribute' => 'sotransnum',
                'width' => '15%',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'sotransdate',
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
                    'class' => 'text-left'
                ],
            ],
            [
                'attribute' => 'addressbookid',
                'value' => 'customer.fullname',
                'width' => '15%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-left'
                ],
            ],
            AppHelper::getDataSalesType(),
            [
                'attribute' => 'grandtotal',
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