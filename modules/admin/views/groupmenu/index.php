<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\components\AppHelper;
use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\GroupaccessSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usergroup-index">
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
                ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'groupaccessid',
                'value' => 'groupaccess.groupname',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'menuaccessid',
                'value' => 'menuaccess.description',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'isread', 
                'vAlign' => 'middle'
            ], 
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'iswrite', 
                'vAlign' => 'middle'
            ], 
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'ispost', 
                'vAlign' => 'middle'
            ], 
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'isreject', 
                'vAlign' => 'middle'
            ], 
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'isupload', 
                'vAlign' => 'middle'
            ], 
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'isdownload', 
                'vAlign' => 'middle'
            ], 
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'ispurge', 
                'vAlign' => 'middle'
            ], 
            AppHelper::getSimpleActionGrid(['view', 'update', 'delete'])
        ],
    ]);
    ?>

</div>