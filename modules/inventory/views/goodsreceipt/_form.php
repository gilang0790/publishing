<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\common\models\search\SlocSearchModel;
use app\modules\common\models\search\StoragebinSearchModel;
use app\modules\admin\models\Menuaccess;
use wbraganca\dynamicform\DynamicFormWidget;

?>
<style>
#timer_div {
list-style: none;
position: fixed;
z-index: 5;
right: 0;
top: 180px;
width: 60px;
padding: 8px 5px;
border: 3px solid #fff;
border-right: none;
-moz-border-radius: 10px 0 0 10px;
-webkit-border-radius: 10px 0 0 10px;
border-radius: 10px 0 0 10px;
-moz-box-shadow: 0 0 7px rgba(0, 0, 0, .6);
-webkit-box-shadow: 0 0 7px rgba(0, 0, 0, .6);
box-shadow: 0 0 7px rgba(0, 0, 0, .6);
background: rgba(239, 91, 10, .75);
background: -moz-linear-gradient(top, rgba(243, 52, 8, .75), rgba(239, 91, 10, .75));
background: -webkit-gradient( linear, left top, left bottom, from( rgba(243, 52, 8, .75) ), to( rgba(239, 91, 10, .75) ) );
background: linear-gradient(top, rgba(243, 52, 8, .75), rgba(239, 91, 10, .75));
font-weight: bold;
font-size: 15pt;
}
</style>

<?php if (!isset($isView)): ?>
<div id="timer_div" class="text-right">15:00</div>
<?php endif; ?>
<div class="box box-primary">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'validateOnSubmit' => false,
        'options' => [
            'id' => 'form-goods-receipt'
        ],
    ]); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-10">
                    <h3><?= Menuaccess::getFormName($this->title, Yii::$app->controller->action->id) ?></h3>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Informasi Transaksi</h4></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">                            
                            <?=
                                $form->field($model, 'grtransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'grtransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'slocid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(SlocSearchModel::dropdownList()->orderBy('slocid')->all(),
                                        'slocid', 'sloccode'),
                                'options' => [
                                    'prompt' => '--- Pilih Gudang ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Detail Transaksi</h4></div>
                <div class="panel-body">
                    <div class="row form-group">
                        <div class="col-md-12"><p>*Daftar rak akan tersedia setelah Anda memilih gudang.</p></div>
                        <div class="col-md-12"><p>*Jika terdapat kelebihan barang, masukan ke dalam Jumlah Gratis.</p></div>
                    </div>
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper',  // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items',          // required: css class selector
                        'widgetItem' => '.item',                     // required: css class
                        'limit' => 999,                                // the maximum times, an element can be cloned (default 999)
                        'min' => 1,                                  // 0 or 1 (default 1)
                        'insertButton' => '.add-item',               // css class
                        'deleteButton' => '.remove-item',            // css class
                        'model' => $details[0],
                        'formId' => 'form-goods-receipt',
                        'formFields' => [
                            'head_id',
                            'productid',
                            'qty',
                            'freeqty',
                            'storagebinid'
                        ],
                    ]); ?>
                    
                    <div class="container-items">
                        <?php foreach ($details as $i => $detail): ?>
                        <div class="item row">
                            <?php
                                // necessary for update action.
                                if (!$detail->isNewRecord) {
                                    echo Html::activeHiddenInput($detail, "[{$i}]id");
                                }
                            ?>
                            <div class="col-sm-10 col-md-5">
                                <?= $form->field($detail, "[{$i}]productid")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map(ProductSearchModel::findActive()
                                            ->andWhere(["type" => Product::STOCK])->orderBy('productid')->all(), 'productid', 'productname'),
                                    'options' => [
                                        'prompt' => '--- Pilih Barang ---',
                                        'class' => 'detailProductID'
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]); ?>
                                <?= $form->field($detail, "[{$i}]productid")->hiddenInput()->label(false); ?>
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <?= $form->field($detail, "[{$i}]qty")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control text-right grdetailqty']) ?>
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <?= $form->field($detail, "[{$i}]freeqty")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control text-right grdetailfreeqty']) ?>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <?= $form->field($detail, "[{$i}]storagebinid")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map(StoragebinSearchModel::dropdownList()->orderBy('storagebinid')->all(),
                                            'storagebinid', 'description'),
                                    'options' => [
                                        'prompt' => '--- Pilih Rak ---',
                                        'class' => 'detailStoragebinID'
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php DynamicFormWidget::end(); ?>
                    
                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Gambar</h4></div>
                        <div class="panel-body">
                            <?php if ($model->isNewRecord != '1' && !empty($model->image)): ?>
                                <div style="text-align: center;">
                                    <?=
                                    Html::img('data:image/png;base64,' . $model->image, 
                                        ['id' => 'logo'])
                                    ?>
                                </div>
                                <br /><br />
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            <i class="glyphicon glyphicon-folder-open"></i>&nbsp; Browse&hellip; <input type="file" id="goodsreceiptform-imageFile" name="GoodsreceiptForm[imageFile]" style="display: none;" accept="image/*">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" readonly>
                                    <input id="goodsreceiptform-deleteimage" type="hidden" name="GoodsreceiptForm[deleteImage]">
                                    <?=
                                    Html::a("<i class='glyphicon glyphicon-remove'></i> " . Yii::t('app', 'Delete'), '#',
                                        ['id' => 'btnDeleteImg', 'class' => 'btn btn-danger input-group-addon'])
                                    ?>
                                </div>
                            <?php else: ?>
                                <div id="noImgDiv" style="text-align: center;">
                                    <?=
                                    Html::img(Yii::$app->request->baseUrl . '/assets_b/images/NoImage.png',
                                        ['id' => 'logo', 'style' => 'max-width:220px;max-height:220px;'])
                                    ?>
                                </div>
                                <br /><br />
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="file" id="goodsreceiptform-imageFile" name="GoodsreceiptForm[imageFile]" style="display: none;" accept="image/*">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" readonly>
                                    <input id="goodsreceiptform-deleteimage" type="hidden" name="GoodsreceiptForm[deleteImage]">
                                    <?=
                                    Html::a("<i class='glyphicon glyphicon-remove'></i> " . Yii::t('app', 'Delete'), '#',
                                        ['id' => 'btnDeleteImg', 'class' => 'btn btn-danger input-group-addon hidden'])
                                    ?>
                                </div>
                            <?php endif; ?>
                            <?= Html::activeHiddenInput($model, 'image', ['id' => 'image']) ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Ringkasan Transaksi</h4></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'headernote')->textArea(['row' => 6]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?php if (isset($isView)): ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
        <?php endif; ?>
        <?php if (isset($isCreate)): ?>
        <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Simpan', ['class' => 'btn btn-primary btnSave']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['browse-sloc'], ['class'=>'btn btn-danger']) ?>
        <?php endif; ?>
        <?php if (isset($isUpdate)): ?>
        <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Simpan', ['class' => 'btn btn-primary btnSave']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$imgSrc = Yii::$app->request->baseUrl . '/assets_b/images/NoImage.png';

$js = <<< SCRIPT
var isSubmit = 0;
$(document).ready(function () {
    var time = 900;//in seconds
	var interval = setInterval(function() {
		--time;
		document.getElementById('timer_div').innerHTML = secondToString(time);
		
		if (time <= 0)
		{
			clearInterval(interval);
			window.location.replace("index");
		}
	}, 1000);
    
    getStoragebin();
    
    $(document).on('change', '#goodsreceiptform-slocid', function() {
        getStoragebin();
    });
    
    function getStoragebin() {
        var slocid = $('#goodsreceiptform-slocid').val();
        var i = 0;
        $('.detailProductID').each(function(){
            var productID = 'goodsreceiptdetail-'+i+'-productid';
            var qtyID = 'goodsreceiptdetail-'+i+'-qty';
            var storagebinID = 'goodsreceiptdetail-'+i+'-storagebinid';
            $.ajax({
                type:"POST",
                cache:false,
                url:"lists",
                data:{slocid:slocid},
                success:function(data){
                    console.log(data);
                    $('#'+storagebinID+' option').remove();
                    $('#'+storagebinID).append(data);
                    $('#'+storagebinID).change();
                },
            });
            i += 1;
        });
    }
    
    $('.box-footer').on('click', '.btnSave', function (e) {
        e.preventDefault();
        
        var dateEmptyCounter = 0;
        var slocEmptyCounter = 0;
        var productEmptyCounter = 0;
        var qtyEmptyCounter = 0;
        var storagebinEmptyCounter = 0;
    
        if (!$('#goodsreceiptform-grtransdate-disp').val()) {
            dateEmptyCounter += 1;
            $('.field-goodsreceiptform-grtransdate').addClass('has-error')
            alert('Tanggal Transaksi harus diisi!');
            return false;
        }
    
        if (!$('#goodsreceiptform-slocid').val()) {
            slocEmptyCounter += 1;
            $('.field-goodsreceiptform-slocid').addClass('has-error')
            alert('Gudang harus diisi!');
            return false;
        }
    
        var i = 0;
        var productids = [];
        var productID = '';
        $('.detailProductID').each(function(){
            var detailProductID = 'goodsreceiptdetail-'+i+'-productid';
            var detailQtyID = 'goodsreceiptdetail-'+i+'-qty';
            var detailStoragebinID = 'goodsreceiptdetail-'+i+'-storagebinid';
            var detailHppID = 'goodsreceiptdetail-'+i+'-hpp';
            if (!$('#'+detailProductID).val()) {
                productEmptyCounter += 1;
                $('.field-'+detailProductID).addClass('has-error')
                alert('Barang harus diisi!');
                return false;
            }
            if (!$('#'+detailQtyID).val() || $('#'+detailQtyID).val() == 0) {
                qtyEmptyCounter += 1;
                $('.field-'+detailQtyID).addClass('has-error')
                alert('Jumlah harus lebih besar dari 0!');
                return false;
            }
            if (!$('#'+detailStoragebinID).val()) {
                storagebinEmptyCounter += 1;
                $('.field-'+detailStoragebinID).addClass('has-error')
                alert('Rak harus diisi!');
                return false;
            }
            
            productID = $('#'+detailProductID).val();
            if (productids.includes(productID)) {
                productEmptyCounter += 1;
                $('.field-'+detailProductID).addClass('has-error')
                alert('Barang tidak boleh sama!');
                return false;
            }
            productids.push(productID);
            i += 1;
        });
    
        if (productEmptyCounter == 0 && qtyEmptyCounter == 0 && storagebinEmptyCounter == 0) {
            isSubmit = 1;
        }
    
        if (isSubmit == 1) {
            $('#loading-div').show();
            $('#form-goods-receipt').submit();
        }
    });
    
    $('#goodsreceiptform-imageFile').change(function(){
        var filesToUploads = this.files;
        var file = filesToUploads[0];
        var process = true;
        var errMsg = '';
        if (file) {
            var reader = new FileReader();
            var size = this.files[0].size;
    
            if (size > 2097152) {
                process = false;
                errMsg = 'Ukuran gambar tidak boleh melebihi 2 MB.';
            }
    
            if (process) {
                reader.onload = function (e) {
                    $('#goodsreceiptform-deleteimage').val(0);
                    $('#btnDeleteImg').removeClass('hidden');
                    // $("#logo").attr('src', e.target.result);
                    var canvas = document.createElement("canvas");
                    var ctx = canvas.getContext("2d");
                    var img = new Image();
    
                    img.onload = function() {
                        var MAX_WIDTH = 480;
                        var MAX_HEIGHT = 480;
                        var width = img.width;
                        var height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height *= MAX_WIDTH / width;
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width *= MAX_HEIGHT / height;
                                height = MAX_HEIGHT;
                            }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);

                        dataurl = canvas.toDataURL(file.type);
                        $("#logo").attr('src', dataurl);
                        var base64String = dataurl.replace("data:image/png;base64,", "");
                        $("#image").val(base64String);
                    };
                    img.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    });
    
    $('#btnDeleteImg').click(function() {
        var base_url = window.location.origin+"$imgSrc";
        $("#logo").attr('src', base_url);
        $('#goodsreceiptform-deleteimage').val(1);
        $('#btnDeleteImg').addClass('hidden');
    });
	
	function secondToString(time)
	{
		//Minutes and seconds
		var mins = ~~(time / 60);
		var secs = time % 60;
		
		// Hours, minutes and seconds
		var hrs = ~~(time / 3600);
		var mins = ~~((time % 3600) / 60);
		var secs = time % 60;
		
		// Output like "1:01" or "4:03:59" or "123:03:59"
		ret = "";
		
		if (hrs > 0)
			ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
		
		ret += "" + mins + ":" + (secs < 10 ? "0" : "");
		ret += "" + secs;
		return ret;
	}
});
    
SCRIPT;
$this->registerJs($js);
?>