<?php

use app\components\AppHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\inventory\models\search\GoodsissueSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goodsissue-index">
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
                    AppHelper::getToolbarBrowseButton("Tambah")
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'width' => '4%'
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
                'attribute' => 'salesorderid',
                'value' => 'salesorder.sotransnum',
                'width' => '50%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'label' => 'Pelanggan',
                'value' => 'salesorder.customer.fullname',
                'width' => '15%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            AppHelper::getIsActiveColumn(),
            AppHelper::getActionGrid(['view', 'update', 'delete'])
        ],
    ]); ?>
    
</div>