<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
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
            'id' => 'form-goods-receipt-return'
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
                                $form->field($model, 'grrtransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'grrtransdate')->widget(DateControl::className()) ?>
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
                        <div class="col-md-12"><p>*Silahkan pilih gudang. Jika stok barang kosong, maka rak tidak bisa dipilih.</p></div>
                        <div class="col-md-12"><p>*Gudang akan diatur ulang jika menambah detail barang.</p></div>
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
                        'formId' => 'form-goods-receipt-return',
                        'formFields' => [
                            'head_id',
                            'productid',
                            'qty',
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
                            <div class="col-sm-12 col-md-6">
                                <?= $form->field($detail, "[{$i}]productid")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map(ProductSearchModel::findProductReceipt($model->goodsreceiptid)
                                            ->orderBy('productid')->all(), 'productid', 'productname'),
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
                                <?= $form->field($detail, "[{$i}]qty")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control text-right grrdetailqty']) ?>
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
                            <div class="col-sm-3 col-md-1 item-action">
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs">
                                        <i class="glyphicon glyphicon-plus"></i></button> 
                                    <button type="button" class="remove-item btn btn-danger btn-xs">
                                        <i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php DynamicFormWidget::end(); ?>
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
    
    $(document).on('change', '#goodsreceiptreturnform-slocid', function() {
        getStoragebin();
    });
    
    function getStoragebin() {
        var slocid = $('#goodsreceiptreturnform-slocid').val();
        var i = 0;
        $('.detailProductID').each(function(){
            var productID = 'goodsreceiptdetailreturn-'+i+'-productid';
            var qtyID = 'goodsreceiptdetailreturn-'+i+'-qty';
            var storagebinID = 'goodsreceiptdetailreturn-'+i+'-storagebinid';
            var product = $('#'+productID).val();
            var qty = $('#'+qtyID).val();
            $.ajax({
                type:"POST",
                cache:false,
                url:"lists",
                data:{slocid:slocid, productid:product, qty:qty},
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
    
    $('.item-action').on('click', '.add-item', function (e) {
        e.preventDefault();
        
        $('#goodsreceiptreturnform-slocid').val(0).trigger('change');
    });
    
    $('.box-footer').on('click', '.btnSave', function (e) {
        e.preventDefault();
        
        var dateEmptyCounter = 0;
        var slocEmptyCounter = 0;
        var productEmptyCounter = 0;
        var qtyEmptyCounter = 0;
        var storagebinEmptyCounter = 0;
    
        if (!$('#goodsreceiptreturnform-grrtransdate-disp').val()) {
            dateEmptyCounter += 1;
            $('.field-goodsreceiptreturnform-grrtransdate').addClass('has-error')
            alert('Tanggal Transaksi harus diisi!');
            return false;
        }
    
        if (!$('#goodsreceiptreturnform-slocid').val()) {
            slocEmptyCounter += 1;
            $('.field-goodsreceiptreturnform-slocid').addClass('has-error')
            alert('Gudang harus diisi!');
            return false;
        }
    
        var i = 0;
        var productids = [];
        var productID = '';
        $('.detailProductID').each(function(){
            var detailProductID = 'goodsreceiptdetailreturn-'+i+'-productid';
            var detailQtyID = 'goodsreceiptdetailreturn-'+i+'-qty';
            var detailStoragebinID = 'goodsreceiptdetailreturn-'+i+'-storagebinid';
            var detailHppID = 'goodsreceiptdetailreturn-'+i+'-hpp';
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
            $('#form-goods-receipt-return').submit();
        }
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