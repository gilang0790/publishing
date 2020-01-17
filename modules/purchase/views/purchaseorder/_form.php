<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\common\models\search\SupplierSearchModel;
use app\modules\common\models\search\PaymentmethodSearchModel;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\common\models\search\ProductSearchModel;
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
            'id' => 'form-purchase-order'
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
                        <div class="col-md-3">
                            <?=
                                $form->field($model, 'potransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'potransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'plantid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PlantSearchModel::dropdownList()->orderBy('plantid')->all(),
                                        'plantid', 'plantcode'),
                                'options' => [
                                    'prompt' => '--- Pilih Cabang ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'addressbookid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(SupplierSearchModel::findActive()->orderBy('addressbookid')->all(),
                                        'addressbookid', 'fullname'),
                                'options' => [
                                    'prompt' => '--- Pilih Pemasok ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'paymentmethodid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PaymentmethodSearchModel::findActive()->orderBy('paymentmethodid')->all(),
                                        'paymentmethodid', 'paymentname'),
                                'options' => [
                                    'prompt' => '--- Pilih Pembayaran ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'billto')->textarea(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'shipto')->textarea(['class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Detail Transaksi</h4></div>
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper',  // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items',          // required: css class selector
                        'widgetItem' => '.item',                     // required: css class
                        'limit' => 999,                                // the maximum times, an element can be cloned (default 999)
                        'min' => 1,                                  // 0 or 1 (default 1)
                        'insertButton' => '.add-item',               // css class
                        'deleteButton' => '.remove-item',            // css class
                        'model' => $details[0],
                        'formId' => 'form-purchase-order',
                        'formFields' => [
                            'head_id',
                            'productid',
                            'qty',
                            'price',
                            'total'
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
                            <div class="col-sm-8 col-md-4">
                                <?= $form->field($detail, "[{$i}]productid")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map(ProductSearchModel::findActive()->andWhere("type = 1")->orderBy('productid')->all(),
                                            'productid', 'productname'),
                                    'options' => [
                                        'prompt' => '--- Pilih Barang ---',
                                        'class' => 'detailProductID'
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]); ?>
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <?= $form->field($detail, "[{$i}]qty")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control input-decimal text-right purchasedetailqty']) ?>
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <?= $form->field($detail, "[{$i}]price")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control text-right purchasedetailprice']) ?>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <?= $form->field($detail, "[{$i}]total")->textInput(['type' => 'number', 'min' => 1, 'readOnly' => true, 'class' => 'form-control text-right purchasedetailtotal']) ?>
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
                        <div class="col-md-8">
                            <?= $form->field($model, 'headernote')->textArea(['row' => 6]) ?>
                        </div>
                        <div class="col-md-4">
                            <?=
                                $form->field($model, 'grandtotal')
                                ->textInput([
                                    'readonly' => true,
                                    'id' => 'purchaseordertotal',
                                    'class' => 'form-control input-decimal text-right',
                                    'style' => 'font-size: 18px',
                                ])
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?php if (!isset($isView)): ?>
        <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Simpan', 
            ['class' => 'btn btn-primary btnSave']) ?>
        <?php endif; ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['index'], ['class'=>'btn btn-danger']) ?>
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
    
    calculateSummary();
    
    $(document).on('click', '.remove-item', function() {
        calculateSummary();
    });
    
    $(document).on('input', '.purchasedetailqty', function() {
        var str = this.id;
        var priceID = str.replace('qty', 'price');
        var totaldetailID = str.replace('qty', 'total');
        var qty = this.value;
        var price = $('#'+priceID).val();
        calculateItemSummary(qty,price,totaldetailID);
        calculateSummary();
    });
    
    $(document).on('input', '.purchasedetailprice', function() {
        var str = this.id;
        var qtyID = str.replace('price', 'qty');
        var totaldetailID = str.replace('price', 'total');
        var price = this.value;
        var qty = $('#'+qtyID).val();
        calculateItemSummary(qty,price,totaldetailID);
        calculateSummary();
    });
    
    function calculateItemSummary(qty,rawprice,totaldetailID) {
        var price = rawprice.replace(".", "");
        var subtotal = qty*price;
        var finaltotal = subtotal;
        $('#'+totaldetailID).val(finaltotal);
    }
    
    function calculateSummary() {
        var i = 0;
        var total = 0;
        $('.purchasedetailtotal').each(function(){
            var totaldetailID = 'purchaseorderdetail-'+i+'-total';
            var subtotal = $('#'+totaldetailID).val();
            total += parseInt(subtotal);
            i += 1;
        });
        $('#purchaseordertotal').val(total);
    };
    
    $('.box-footer').on('click', '.btnSave', function (e) {
        e.preventDefault();
        
        var dateEmptyCounter = 0;
        var plantEmptyCounter = 0;
        var supplierEmptyCounter = 0;
        var paymentEmptyCounter = 0;
        var productEmptyCounter = 0;
        var qtyEmptyCounter = 0;
        var priceEmptyCounter = 0;
    
        if (!$('#purchaseorderform-potransdate-disp').val()) {
            dateEmptyCounter += 1;
            $('.field-purchaseorderform-potransdate').addClass('has-error')
            alert('Tanggal Transaksi harus diisi!');
            return false;
        }
    
        if (!$('#purchaseorderform-plantid').val()) {
            plantEmptyCounter += 1;
            $('.field-purchaseorderform-plantid').addClass('has-error')
            alert('Cabang harus diisi!');
            return false;
        }
    
        if (!$('#purchaseorderform-addressbookid').val()) {
            supplierEmptyCounter += 1;
            $('.field-purchaseorderform-addressbookid').addClass('has-error')
            alert('Pemasok harus diisi!');
            return false;
        }
    
        if (!$('#purchaseorderform-paymentmethodid').val()) {
            paymentEmptyCounter += 1;
            $('.field-purchaseorderform-paymentmethodid').addClass('has-error')
            alert('Cara Pembayaran harus diisi!');
            return false;
        }
    
        var i = 0;
        var productids = [];
        var productID = '';
        $('.detailProductID').each(function(){
            var detailProductID = 'purchaseorderdetail-'+i+'-productid';
            var detailQtyID = 'purchaseorderdetail-'+i+'-qty';
            var detailPriceID = 'purchaseorderdetail-'+i+'-price';
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
            if (!$('#'+detailPriceID).val() || $('#'+detailPriceID).val() == 0) {
                priceEmptyCounter += 1;
                $('.field-'+detailPriceID).addClass('has-error')
                alert('Harga harus lebih besar dari 0!');
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
    
        if (productEmptyCounter == 0 && qtyEmptyCounter == 0 && priceEmptyCounter == 0) {
            isSubmit = 1;
        }
    
        if (isSubmit == 1) {
            $('#loading-div').show();
            $('#form-purchase-order').submit();
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