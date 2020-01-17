<?php

use app\components\AppHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use app\modules\admin\models\Menuaccess;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\inventory\models\search\StockopnameSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stockopname-index">
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
                    AppHelper::getToolbarUploadButton('Unggah', 'btnUpload') .
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
                'attribute' => 'bstransnum',
                'width' => '15%',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'bstransdate',
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
                'attribute' => 'slocid',
                'value' => 'sloc.sloccode',
                'width' => '15%',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'headernote',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            AppHelper::getDataWfStatus(),
            AppHelper::getActionGrid(['view', 'update', 'approval', 'reject'])
        ],
    ]); ?>
    
</div>

<!-- Modal Upload -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Unggah Data Barang</h4>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php
                        $form = ActiveForm::begin([
                            'enableAjaxValidation' => true,
                            'options' => [
                                'enctype' => 'multipart/form-data',
                                'id' => 'upload-opname'
                            ],
                        ]);
                        ?>
                        <div class="col-md-12">
                            <?=
                            $form->field($uploadModel, 'fileUpload')->widget(\kartik\file\FileInput::classname(), [
                                'options' => [
                                    'accept' => 'file/*',
                                    'class' => 'file-upload',
                                    'disabled' => isset($isView),
                                ],
                                'pluginOptions' => [
                                    'showPreview' => false,
                                    'showCaption' => true,
                                    'showRemove' => true,
                                    'showUpload' => false,
                                ],
                            ])
                            ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="pull-right">
                            <a href="#" onclick='window.open("<?= Url::to(['download']) ?>");' class="btn btn-warning">
                                <i class="glyphicon glyphicon-save"></i> Unduh Template
                            </a>
                            <?= Html::submitButton('<i class="glyphicon glyphicon-upload"></i> Unggah', ['class' => 'btn btn-primary btnSave']) ?>
                        </div>
                        <div class="clearfix"></div>    
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <div class="modal-footer" style="text-align: left;">
            </div>
        </div>
    </div>
</div>

<?php
$js = <<< SCRIPT
    $(document).ready(function () {
        $("#btnUpload").click(function(){
            $("#myModal").modal();
        });
    
        $('.panel-footer').on('click', '.btnSave', function (e) {
            e.preventDefault();

            $('#loading-div').show();
            $('#upload-opname').submit();
        });
    });
	
    $(document).on('pjax:end', function () {
        $("#btnUpload").click(function(){
            $("#myModal").modal();
        });
    });
SCRIPT;
$this->registerJs($js);
?>