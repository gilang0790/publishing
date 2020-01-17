<?php

use app\modules\admin\models\Menuaccess;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\helpers\Html;


$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stockcard-index">
    <?php
    Pjax::begin([
        'id' => 'search-pjax',
        'enablePushState' => false,
        'timeout' => 100000
    ]);
    ?>
    
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Cari</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <?php echo $this->render('_search', ['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>
    
    <?=
    GridView::widget([
        'id' => 'grid-stock',
        'dataProvider' => $model->searchStock(),
        'pjax' => false,
        'panel' => [
            'heading' => $this->title,
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'width' => '4%'
            ],
            [
                'attribute' => 'plantcode',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'width' => '10%',
            ],
            [
                'attribute' => 'sloccode',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'width' => '10%',
            ],
            [
                'attribute' => 'categoryname',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'width' => '10%',
            ],
            [
                'attribute' => 'productname',
                'contentOptions' => [
                    'class' => 'text-left'
                ],
                'width' => '20%',
            ],
            [
                'attribute' => 'productcode',
                'contentOptions' => [
                    'class' => 'text-left'
                ],
                'width' => '10%',
            ],
            [
                'attribute' => 'uomcode',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'width' => '10%',
            ],
            [
                'attribute' => 'qty',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'width' => '10%'
            ],
            [
                'attribute' => 'stockValue',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'hiddenFromExport' => true,
                'width' => '10%',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'header' => '',
                'width' => '100px',
                'buttons' => [
                    'view' => function ($url, $model) {
                        $resArray = [];
                        $search = Yii::$app->request->queryParams;
                        $searchArray = $search['StockSearchModel'];
                        $resArray['plantid'] = $model->plantid;
                        $resArray['slocid'] = $model->slocid;
                        $resArray['categoryid'] = $model->categoryid;
                        $resArray['productname'] = $model->productname;
                        $resArray['productcode'] = $model->productcode;
                        return Html::a("<span class='glyphicon glyphicon-eye-open'></span>", [
                            'view', 
                            'search' => json_encode($resArray)
                        ], [
                            'title' => 'Kartu Stok',
                            'target' => '_blank',
                            'data-pjax' => '0'
                        ]);
                    },
                ]
            ]
        ],
    ]);
    ?>

    <?php Pjax::end() ?>
</div>
<?php

$js = <<<SCRIPT
$(document).ready(function() {
    $('#search-pjax').on('pjax:start', function() {
        $('#loading-div').show();
    });
    
    $('#search-pjax').on('pjax:end', function() {
        $('#loading-div').hide();
    });
});
SCRIPT;
$this->registerJs($js);
?>