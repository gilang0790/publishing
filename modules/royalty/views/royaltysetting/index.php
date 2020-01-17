<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\components\AppHelper;
use app\modules\admin\models\Menuaccess;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="royaltysetting-index">
    <?=
    GridView::widget([
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
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'addressbookid',
                'value' => 'author.fullname',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'productid',
                'value' => 'product.productname',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'period',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-right'
                ],
                'value' => function($data) {
                    return !empty($data->period) ? $data->period : 0;
                },
                'format' => ['decimal', 0]
            ],
            [
                'attribute' => 'fee',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-right'
                ],
                'value' => function($data) {
                    return !empty($data->fee) ? $data->fee : 0;
                },
                'format' => ['decimal', 0]
            ],
            [
                'attribute' => 'tax',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-right'
                ],
                'value' => function($data) {
                    return !empty($data->tax) ? $data->tax : 0;
                },
                'format' => ['decimal', 0]
            ],
            AppHelper::getIsActiveColumn(),
            AppHelper::getActionGrid(['view', 'update', 'delete'])
        ],
    ]); ?>
    
</div>