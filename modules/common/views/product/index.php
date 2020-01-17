<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\components\AppHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use app\modules\admin\models\Menuaccess;
use app\modules\common\models\Product;

$this->title = Menuaccess::getMenuName(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'panel' => [
            'heading' => Menuaccess::gridTitle($this->title),
        ],
        'toolbar' => [
            [
                'content' =>
                Html::a('<i class="glyphicon glyphicon-export"></i>', ['index'], [
                    'class' => 'btn btn-default',
                    'title' => 'Ekspor Data',
                    'data-pjax' => '0',
                    'data-method' => 'post',
                    'data-params' => [
                        'exportData' => true
                    ],
                ]) .
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], [
                    'class' => 'btn btn-default',
                    'title' => 'Reset'
                ]) .
                AppHelper::getToolbarUploadButton('Unggah', 'btnUpload') .
                AppHelper::getToolbarCreateButton("Tambah")
            ],
        ],
        'filterModel' => $searchModel,
        'export' => [
            'label' => '&nbsp;&nbsp; Ekspor Data',
            'target' => GridView::TARGET_SELF,
            'showConfirmAlert' => false,
        ],
        'exportConfig' => [
            GridView::EXCEL => [],
            GridView::CSV => [],
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'productname',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'productcode',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'unitofmeasureid',
                'value' => 'uom.uomcode',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'categoryid',
                'value' => 'category.categoryname',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'isbn',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'author',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'weight',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'size',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'vAlign' => 'middle'
            ],
            Product::getTypeColumn(),
            AppHelper::getIsActiveColumn(),
            AppHelper::getActionGrid(['view', 'update', 'delete'])
        ],
    ]);
    ?>
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
                                'id' => 'upload-product'
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
            $('#upload-product').submit();
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