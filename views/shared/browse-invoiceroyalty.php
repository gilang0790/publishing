<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\components\AppHelper;
use app\modules\admin\models\Menuaccess;

?>
<div class="invoiceroyalty-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'panel' => [
            'heading' => Menuaccess::gridBrowseInvoiceroyaltyTitle(),
        ],
        'toolbar' => [
            [
                'content' =>
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['browse'], [
                    'class' => 'btn btn-default',
                    'title' => 'Reset'
                ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'transnum',
                'width' => '10%',
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
                'width' => '15%',
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
                'width' => '15%',
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
                'width' => '15%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-right'
                ],
                'format' => ['decimal', 0]
            ],
            AppHelper::getActionGrid(['browse'])
        ],
    ]);
    ?>
    
    <div class="box-footer text-right">
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
    </div>

</div>