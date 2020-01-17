<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\common\models\search\CustomerSearchModel;
use app\modules\common\models\search\PlantSearchModel;
use app\modules\accounting\models\search\InvoicearBrowseModel;
use app\modules\admin\models\Menuaccess;

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
            'id' => 'form-sales-payment'
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
                                $form->field($model, 'sptransnum')
                                ->textInput([
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sptransdate')->widget(DateControl::className()) ?>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <?= $form->field($model, 'invoicearid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(InvoicearBrowseModel::findActive()->orderBy('id')->all(),
                                        'id', 'artransnum'),
                                'options' => [
                                    'prompt' => '--- Pilih Faktur Penjualan ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'addressbookid')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(CustomerSearchModel::findActive()->orderBy('addressbookid')->all(),
                                        'addressbookid', 'fullname'),
                                'options' => [
                                    'prompt' => '--- Pilih Pelanggan ---'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'receiptno')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'bankname')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'bankaccountno')->textInput(['class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'aramount')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'paidamount')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'advanceamount')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'stringPayamount')->textInput(['class' => 'form-control input-decimal text-right']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'headernote')->textarea(['class' => 'form-control']) ?>
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
    
    $('.box-footer').on('click', '.btnSave', function (e) {
        e.preventDefault();
        
        var strAramount = $('#salespaymentform-aramount').val();
        var strPaidamount = $('#salespaymentform-paidamount').val();
        var strAdvanceamount = $('#salespaymentform-advanceamount').val();
        var strPayamount = $('#salespaymentform-stringpayamount').val();
        var aramount = strAramount.split('.').join("");
        var paidamount = strPaidamount.split('.').join("");
        var advanceamount = strAdvanceamount.split('.').join("");
        var payamount = strPayamount.split('.').join("");
    
        var dibayar = parseInt(payamount) + parseInt(advanceamount);
        var sisa = parseInt(aramount) - parseInt(paidamount);

        if (dibayar > sisa) {
            alert('Pembayaran tidak boleh melebihi sisa tagihan.');
            return false;
        } else {
            $('#loading-div').show();
            $('#form-sales-payment').submit();
        }
    
        
    });
});
    
SCRIPT;
$this->registerJs($js);
?>