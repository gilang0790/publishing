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
                ]) .
                AppHelper::getToolbarCreateButton('Tambah')
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'workflowid',
                'value' => 'workflow.wfdesc',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'groupaccessid',
                'value' => 'groupaccess.groupname',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'wfbefstat',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'wfrecstat',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            AppHelper::getActionGrid(['view', 'update', 'delete'])
        ],
    ]);
    ?>

</div>