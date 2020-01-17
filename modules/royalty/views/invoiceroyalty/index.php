<?php

use app\components\AppHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-royalty-index">
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
                    ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'width' => '4%'
            ],
            [
                'attribute' => 'transnum',
                'width' => '15%',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'transdate',
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
                'width' => '12%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-left'
                ],
            ],
            [
                'attribute' => 'addressbookid',
                'value' => 'author.fullname',
                'width' => '12%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'productid',
                'value' => 'product.productname',
                'width' => '15%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'amount',
                'width' => '12%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-right'
                ],
                'format' => ['decimal', 0]
            ],
            [
                'attribute' => 'payamount',
                'width' => '12%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-right'
                ],
                'format' => ['decimal', 0]
            ],
            AppHelper::getActionGrid(['view'])
        ],
    ]); ?>
    
</div>