<?php

use app\modules\admin\models\Menuaccess;
use kartik\grid\GridView;
use yii\widgets\Pjax;


$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-card-view-index">
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
                    <?php echo $this->render('_search-stock-card', ['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>

    <?=
    GridView::widget([
        'id' => 'grid-stock',
        'dataProvider' => $model->searchStockPeriod(),
        'pjax' => false,
        'showPageSummary' => true,
        'panel' => [
            'heading' => 'Kartu Persediaan',
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'width' => '4%'
            ],
            [
                'attribute' => 'transdate',
                'format' => ['date', 'php:d-m-Y'],
                'hAlign' => 'center',
                'width' => '10%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
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
                'attribute' => 'refnum',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'width' => '10%',
            ],
            [
                'attribute' => 'qtyin',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'width' => '10%',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'attribute' => 'qtyout',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'width' => '10%',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ]
        ],
    ]);
    ?>

    <?php Pjax::end() ?>
</div>
<?php
$js = <<<SCRIPT
$(document).ready(function() {
    $('.kv-expand-header-cell').removeData('original-title');
        
    $('#search-pjax').on('pjax:start', function() {
        showLoading();
    });
    
    $('#search-pjax').on('pjax:end', function() {
        hideLoading();    
    });
});
SCRIPT;
$this->registerJs($js);
?>
