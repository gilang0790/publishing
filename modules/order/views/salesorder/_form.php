<?php

use app\components\AppHelper;
use app\modules\admin\models\Menuaccess;
use app\modules\common\models\search\CustomerSearchModel;
use app\modules\common\models\search\PaymentmethodSearchModel;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\common\models\search\ProductSearchModel;
use kartik\datecontrol\DateControl;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
            'id' => 'form-sales-order'
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
                                $form->field($model, 'sotransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sotransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'plantid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PlantSearchModel::dropdownList()->orderBy('plantid')->all(),
                                        'plantid', 'plantcode'),
                                'options' => [
                                    'prompt' => '--- Pilih ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'addressbookid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(CustomerSearchModel::findActive()->orderBy('fullname')->all(),
                                        'addressbookid', 'fullname'),
                                'options' => [
                                    'prompt' => '--- Pilih ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'pocustomer')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'salestype')->widget(Select2::classname(), [
                                'data' => AppHelper::$salesType,
                                'options' => [
                                    'prompt' => '--- Pilih ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'paymentmethodid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PaymentmethodSearchModel::findActive()->orderBy('paymentmethodid')->all(),
                                        'paymentmethodid', 'paymentname'),
                                'options' => [
                                    'prompt' => '--- Pilih ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'dueDate')->widget(DateControl::className()) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'address')->textarea(['class' => 'form-control']) ?>
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
                        'formId' => 'form-sales-order',
                        'formFields' => [
                            'head_id',
                            'productid',
                            'qty',
                            'price',
                            'vat',
                            'discount',
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
                            <div class="col-sm-2 col-md-1">
                                <?= $form->field($detail, "[{$i}]qty")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control input-decimal text-right salesdetailqty']) ?>
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <?= $form->field($detail, "[{$i}]price")->textInput(['type' => 'number', 'min' => 1, 'class' => 'form-control input-decimal text-right salesdetailprice']) ?>
                            </div>
                            <div class="col-sm-2 col-md-1">
                                <?= $form->field($detail, "[{$i}]vat")->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control input-decimal text-right salesdetailvat']) ?>
                                <?= Html::activeHiddenInput($detail, "[{$i}]totalvat"); ?>
                            </div>
                            <div class="col-sm-2 col-md-1">
                                <?= $form->field($detail, "[{$i}]discount")->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control input-decimal text-right salesdetaildiscount']) ?>
                                <?= Html::activeHiddenInput($detail, "[{$i}]totaldiscount"); ?>
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <?= $form->field($detail, "[{$i}]total")->textInput(['type' => 'number', 'min' => 1, 'readOnly' => true, 'class' => 'form-control input-decimal text-right salesdetailtotal']) ?>
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
                                    'id' => 'salesordertotal',
                                    'class' => 'form-control input-decimal text-right',
                                    'style' => 'font-size: 18px',
                                ])
                            ?>
                            <?=
                                Html::activeHiddenInput($model, "totalvat", ['id' => 'salesordertotalvat']);
                            ?>
                            <?=
                                Html::activeHiddenInput($model, "totaldiscount", ['id' => 'salesordertotaldiscount']);
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
    
    $(document).on('input', '.salesdetailqty', function() {
        var str = this.id;
        var priceID = str.replace('qty', 'price');
        var discountID = str.replace('qty', 'discount');
        var vatID = str.replace('qty', 'vat');
        var totaldiscountID = str.replace('qty', 'totaldiscount');
        var totalvatID = str.replace('qty', 'totalvat');
        var totaldetailID = str.replace('qty', 'total');
        var qty = this.value;
        var price = $('#'+priceID).val();
        var discount = $('#'+discountID).val();
        var vat = $('#'+vatID).val();
        calculateItemSummary(qty,price,discount,vat,totaldiscountID,totalvatID,totaldetailID);
        calculateSummary();
    });
    
    $(document).on('input', '.salesdetailprice', function() {
        var str = this.id;
        var qtyID = str.replace('price', 'qty');
        var discountID = str.replace('price', 'discount');
        var vatID = str.replace('price', 'vat');
        var totaldiscountID = str.replace('price', 'totaldiscount');
        var totalvatID = str.replace('price', 'totalvat');
        var totaldetailID = str.replace('price', 'total');
        var price = this.value;
        var qty = $('#'+qtyID).val();
        var discount = $('#'+discountID).val();
        var vat = $('#'+vatID).val();
        calculateItemSummary(qty,price,discount,vat,totaldiscountID,totalvatID,totaldetailID);
        calculateSummary();
    });
    
    $(document).on('input', '.salesdetaildiscount', function() {
        var str = this.id;
        var qtyID = str.replace('discount', 'qty');
        var priceID = str.replace('discount', 'price');
        var vatID = str.replace('discount', 'vat');
        var totaldiscountID = str.replace('discount', 'totaldiscount');
        var totalvatID = str.replace('discount', 'totalvat');
        var totaldetailID = str.replace('discount', 'total');
        var discount = this.value;
        var qty = $('#'+qtyID).val();
        var price = $('#'+priceID).val();
        var vat = $('#'+vatID).val();
        calculateItemSummary(qty,price,discount,vat,totaldiscountID,totalvatID,totaldetailID);
        calculateSummary();
    });
    
    $(document).on('input', '.salesdetailvat', function() {
        var str = this.id;
        var qtyID = str.replace('vat', 'qty');
        var priceID = str.replace('vat', 'price');
        var discountID = str.replace('vat', 'discount');
        var totaldiscountID = str.replace('vat', 'totaldiscount');
        var totalvatID = str.replace('vat', 'totalvat');
        var totaldetailID = str.replace('vat', 'total');
        var vat = this.value;
        var qty = $('#'+qtyID).val();
        var price = $('#'+priceID).val();
        var discount = $('#'+discountID).val();
        calculateItemSummary(qty,price,discount,vat,totaldiscountID,totalvatID,totaldetailID);
        calculateSummary();
    });
    
    function calculateItemSummary(qty,rawprice,discount,vat,totaldiscountID,totalvatID,totaldetailID) {
        var price = rawprice.replace(".", "");
        var subtotal = qty*price;
        var finalvat = subtotal*vat/100;
        if (discount > 0) {
            var finaldiscount = subtotal*discount/100;
            var finaltotal = subtotal+finalvat-finaldiscount;
            $('#'+totaldiscountID).val(finaldiscount);
            $('#'+totaldetailID).val(finaltotal);
        } else if (discount == 0) {
            var finaltotal = subtotal+finalvat;
            $('#'+totaldiscountID).val(discount);
            $('#'+totaldetailID).val(finaltotal);
        }
        $('#'+totalvatID).val(finalvat);
    }
    
    function calculateSummary() {
        var i = 0;
        var totaldiscount = 0;
        var totalvat = 0;
        var total = 0;
        $('.salesdetailtotal').each(function(){
            var totaldiscountID = 'salesorderdetail-'+i+'-totaldiscount';
            var totalvatID = 'salesorderdetail-'+i+'-totalvat';
            var totaldetailID = 'salesorderdetail-'+i+'-total';
            var subtotaldiscount = $('#'+totaldiscountID).val();
            var subtotalvat = $('#'+totalvatID).val();
            var subtotal = $('#'+totaldetailID).val();
            totaldiscount += parseInt(subtotaldiscount);
            totalvat += parseInt(subtotalvat);
            total += parseInt(subtotal);
            i += 1;
        });
        $('#salesordertotalvat').val(totalvat);
        $('#salesordertotaldiscount').val(totaldiscount);
        $('#salesordertotal').val(total);
    };
    
    $('.box-footer').on('click', '.btnSave', function (e) {
        e.preventDefault();
        
        var dateEmptyCounter = 0;
        var plantEmptyCounter = 0;
        var customerEmptyCounter = 0;
        var typeEmptyCounter = 0;
        var productEmptyCounter = 0;
        var qtyEmptyCounter = 0;
        var priceEmptyCounter = 0;
        var vatEmptyCounter = 0;
        var discountEmptyCounter = 0;
    
        if (!$('#salesorderform-sotransdate-disp').val()) {
            dateEmptyCounter += 1;
            $('.field-salesorderform-sotransdate').addClass('has-error')
            alert('Tanggal Transaksi harus diisi!');
            return false;
        }
    
        if (!$('#salesorderform-plantid').val()) {
            plantEmptyCounter += 1;
            $('.field-salesorderform-plantid').addClass('has-error')
            alert('Cabang harus diisi!');
            return false;
        }
    
        if (!$('#salesorderform-addressbookid').val()) {
            customerEmptyCounter += 1;
            $('.field-salesorderform-addressbookid').addClass('has-error')
            alert('Pelanggan harus diisi!');
            return false;
        }
    
        if (!$('#salesorderform-salestype').val()) {
            typeEmptyCounter += 1;
            $('.field-salesorderform-salestype').addClass('has-error')
            alert('Jenis Penjualan harus diisi!');
            return false;
        }
    
        var i = 0;
        var productids = [];
        var productID = '';
        $('.detailProductID').each(function(){
            var detailProductID = 'salesorderdetail-'+i+'-productid';
            var detailQtyID = 'salesorderdetail-'+i+'-qty';
            var detailPriceID = 'salesorderdetail-'+i+'-price';
            var detailVatID = 'salesorderdetail-'+i+'-vat';
            var detailDiscountID = 'salesorderdetail-'+i+'-discount';
            if (!$('#'+detailProductID).val()) {
                productEmptyCounter += 1;
                $('.field-'+detailProductID).addClass('has-error')
                alert('Barang harus diisi!');
                return false;
            }
            if (!$('#'+detailQtyID).val() || $('#'+detailQtyID).val() < 1) {
                qtyEmptyCounter += 1;
                $('.field-'+detailQtyID).addClass('has-error')
                alert('Jumlah harus lebih besar dari 0!');
                return false;
            }
            if (!$('#'+detailPriceID).val() || $('#'+detailPriceID).val() < 0) {
                priceEmptyCounter += 1;
                $('.field-'+detailPriceID).addClass('has-error')
                alert('Harga harus lebih besar dari 0!');
                return false;
            }
            if (!$('#'+detailVatID).val() || $('#'+detailVatID).val() < 0) {
                vatEmptyCounter += 1;
                $('.field-'+detailVatID).addClass('has-error')
                alert('Jika tidak ada PPN, isilah dengan nilai 0!');
                return false;
            }
            if (!$('#'+detailDiscountID).val() || $('#'+detailDiscountID).val() < 0) {
                discountEmptyCounter += 1;
                $('.field-'+detailDiscountID).addClass('has-error')
                alert('Jika tidak ada diskon, isilah dengan nilai 0!');
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
    
        if (productEmptyCounter == 0 && qtyEmptyCounter == 0 && priceEmptyCounter == 0 && vatEmptyCounter == 0 && discountEmptyCounter == 0) {
            isSubmit = 1;
        }
    
        if (isSubmit == 1) {
            $('#loading-div').show();
            $('#form-sales-order').submit();
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