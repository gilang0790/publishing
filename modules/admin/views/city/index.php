<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\CitySearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-index">
            <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'panel' => [
            'heading' => 'City List',
        ],
        'toolbar' => [
            [
                'content' =>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Reset'
                    ]) .
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], [
                        'class' => 'btn btn-primary',
                        'title' => 'Create City',
                        'data-pjax' => '0'
                    ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

                    'cityid',
            'provinceid',
            'citycode',
            'cityname',
            'status',

        ['class' => 'kartik\grid\ActionColumn'],
        ],
        ]); ?>
    
</div>