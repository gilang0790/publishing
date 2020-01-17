<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\components\AppHelper;
use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\GroupaccessSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="sloc-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'panel' => [
            'heading' => Menuaccess::gridBrowseSlocTitle(),
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
                'attribute' => 'sloccode',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'plantid',
                'value' => 'plant.description',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'description',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            AppHelper::getIsActiveColumn(),
            AppHelper::getActionGrid(['browse'])
        ],
    ]);
    ?>
    
    <div class="box-footer text-right">
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
    </div>

</div>