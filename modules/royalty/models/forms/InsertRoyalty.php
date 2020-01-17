<?php
namespace app\modules\royalty\models\forms;

use Exception;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use app\components\AppHelper;
use app\modules\accounting\models\Invoicear;
use app\modules\accounting\models\Invoiceardetail;
use app\modules\royalty\models\Invoiceroyalty;
use app\modules\royalty\models\Invoiceroyaltydetail;
use app\modules\royalty\models\Royalty;
use app\modules\royalty\models\Royaltydetail;
use app\modules\royalty\models\Royaltysetting;
use app\modules\order\models\Salesorder;
use app\modules\order\models\Salesorderdetail;
use app\modules\admin\models\Wfgroup;

class InsertRoyalty extends Model {
    public $productid;
    public $salesorderid;
    public $invoicearid;
    public $royaltysettingid;
    public $salesModel;
    public $sodetailModel;
    public $invoiceModel;
    public $invoicedetailModel;
    public $royaltySettingModel;
    
    public function rules() {
        return [
            [['productid', 'salesorderid', 'invoicearid', 'royaltysettingid'], 'required'],
            [['salesorderid'], 'validateSales'],
            [['invoicearid'], 'validateInvoice'],
            [['royaltysettingid'], 'validateSetting']
        ];
    }
    
    public function validateSales($attribute) {
        if ($this->salesorderid) {
            $maxStatus = Wfgroup::getMaxStatus('appso');
            $this->salesModel = Salesorder::find()->where(['id' => $this->salesorderid, 'status' => $maxStatus])->one();
        }
        if (!$this->salesModel) {
            $this->addError($attribute, 'ID Penjualan Tidak Valid');
        }
        $this->sodetailModel = Salesorderdetail::find()->where([
            'head_id' => $this->salesModel->id])->one();
        if (!$this->sodetailModel) {
            $this->addError($attribute, 'ID Detail Penjualan Tidak Valid');
        }
    }
    
    public function validateInvoice($attribute) {
        if ($this->invoicearid) {
            $maxStatus = Wfgroup::getMaxStatus('appar');
            $this->invoiceModel = Invoicear::find()->where(['id' => $this->invoicearid, 'status' => $maxStatus])->one();
        }
        if (!$this->invoiceModel) {
            $this->addError($attribute, 'ID Invoice Tidak Valid');
        }
        $this->invoicedetailModel = Invoiceardetail::find()->where([
            'head_id' => $this->invoiceModel->id])->one();
        if (!$this->invoicedetailModel) {
            $this->addError($attribute, 'ID Detail Invoice Tidak Valid');
        }
    }
    
    public function validateSetting($attribute) {
        if ($this->royaltysettingid) {
            $this->royaltySettingModel = Royaltysetting::find()->where([
                'royaltysettingid' => $this->royaltysettingid, 
                'productid' => $this->productid])->one();
        }
        if (!$this->royaltySettingModel) {
            $this->addError($attribute, 'ID Pengaturan Royalti Tidak Valid');
        }
    }
    
    public function save() {
        if (!$this->validate()) {
            return false;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // @Notes: Start - Insert to tr_royalty
            $royaltyModel = Royalty::find()->where([
                'royaltysettingid' => $this->royaltySettingModel->royaltysettingid,
                'addressbookid' => $this->royaltySettingModel->addressbookid,
                'productid' => $this->productid])
                ->one();
            
            // Define Next Fee Date
            $nextFeeDate = NULL;
            $currentDate = date('Y-m-d');
            $invoiceRoyalty = Invoiceroyalty::find()->where([
                'royaltysettingid' => $this->royaltySettingModel->royaltysettingid,
                'addressbookid' => $this->royaltySettingModel->addressbookid,
                'productid' => $this->royaltySettingModel->productid
            ])
            ->andWhere('transdate > CURDATE()')
            ->orderBy('transdate DESC')
            ->one();

            $explode = explode('-', $currentDate);
            $startDate = $explode[0] . '-' . $explode[1] . '-01';
            $time = strtotime($startDate);
            $period = $this->royaltySettingModel->period;
            if ($invoiceRoyalty) {
                $nextFeeDate = $invoiceRoyalty->transdate;
            } else {
                $nextFeeDate = date("Y-m-d", strtotime("+".$period." month", $time));
            }
            
            if (!$royaltyModel) {
                $royaltyModel = new Royalty();
                $royaltyModel->plantid = $this->invoiceModel->plantid;
                $royaltyModel->royaltysettingid = $this->royaltySettingModel->royaltysettingid;
                $royaltyModel->addressbookid = $this->royaltySettingModel->addressbookid;
                $royaltyModel->productid = $this->royaltySettingModel->productid;
                $royaltyModel->totalqty = $this->invoicedetailModel->qty;
                $royaltyModel->price = $this->invoicedetailModel->price;
                $royaltyModel->discount = $this->invoicedetailModel->discount;
                $royaltyModel->totaldiscount = $this->invoicedetailModel->totaldiscount;
                $royaltyModel->vat = $this->invoicedetailModel->vat;
                $royaltyModel->totalvat = $this->invoicedetailModel->totalvat;
                $royaltyModel->totalsales = $this->invoicedetailModel->total;
                $royaltyModel->tax = $this->royaltySettingModel->tax;
                $royaltyModel->totaltax = $royaltyModel->totalsales * $this->royaltySettingModel->tax / 100;
                $royaltyModel->fee = $this->royaltySettingModel->fee;
                $royaltyModel->totalfee = $royaltyModel->totalsales * $this->royaltySettingModel->fee / 100;
                $royaltyModel->dueperiod = $this->royaltySettingModel->period;
                $royaltyModel->nextfeedate = $nextFeeDate;
                $royaltyModel->invoicearid = $this->invoicearid;
                $royaltyModel->goodsissueid = $this->invoiceModel->goodsissueid;
                $royaltyModel->salesorderid = $this->invoiceModel->goodsissue->salesorderid;
                $royaltyModel->createdAt = date('Y-m-d H:i:s');
                $royaltyModel->createdBy = Yii::$app->user->identity->userID;
                if (!$royaltyModel->save()) {
                    foreach ($royaltyModel->errors as $errors) {
                        foreach ($errors as $attribute => $error) {
                            throw new Exception('Gagal simpan data: ' . $error);
                        }
                    }
                }
            }
            $this->insertRoyaltyDetail($royaltyModel);
            $this->calculateTotal();
            // @Notes: End - Insert to tr_royalty

            // @Notes: Begin - Insert to tr_invoiceroyalty
            if (!$invoiceRoyalty) {
                $newTransNum = AppHelper::createNewTransactionNumber('Royalty Account Payable', date('Y-m-d'));
                if ($newTransNum == "") {
                    $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                    throw new Exception($errMsg);
                }
                $invoiceRoyalty = new Invoiceroyalty();
                $invoiceRoyalty->transdate = $nextFeeDate;
                $invoiceRoyalty->transnum = $newTransNum;
                $invoiceRoyalty->plantid = $this->invoiceModel->plantid;
                $invoiceRoyalty->royaltysettingid = $this->royaltySettingModel->royaltysettingid;
                $invoiceRoyalty->addressbookid = $this->royaltySettingModel->addressbookid;
                $invoiceRoyalty->productid = $this->royaltySettingModel->productid;
                $invoiceRoyalty->totalqty = $this->invoicedetailModel->qty;
                $invoiceRoyalty->amount = $this->invoicedetailModel->total * $this->royaltySettingModel->fee / 100;
                $invoiceRoyalty->payamount = 0;
                $invoiceRoyalty->createdAt = date('Y-m-d H:i:s');
                $invoiceRoyalty->createdBy = Yii::$app->user->identity->userID;
                if (!$invoiceRoyalty->save()) {
                    foreach ($invoiceRoyalty->errors as $errors) {
                        foreach ($errors as $attribute => $error) {
                            throw new Exception('Gagal simpan data: ' . $error);
                        }
                    }
                }
            }
            $this->insertInvoiceRoyaltyDetail($invoiceRoyalty);
            $this->calculateInvoiceTotal();
            
            // @Notes: End - Insert to tr_invoiceroyalty
            
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            $this->addError('salesModel', $ex->getMessage());
            return false;
        }
    }
    
    private function insertRoyaltyDetail($royalty) {
        $royaltyDetail = new Royaltydetail();
        $royaltyDetail->head_id = $royalty->id;
        $royaltyDetail->addressbookid = $royalty->addressbookid;
        $royaltyDetail->productid = $royalty->productid;
        $royaltyDetail->qty = $this->invoicedetailModel->qty;
        $royaltyDetail->price = $this->invoicedetailModel->price;
        $royaltyDetail->discount = $this->invoicedetailModel->price;
        $royaltyDetail->totaldiscount = $this->invoicedetailModel->totaldiscount;
        $royaltyDetail->vat = $this->invoicedetailModel->vat;
        $royaltyDetail->totalvat = $this->invoicedetailModel->totalvat;
        $royaltyDetail->totalsales = $this->invoicedetailModel->total;
        $royaltyDetail->tax = $this->royaltySettingModel->tax;
        $royaltyDetail->totaltax = $royaltyDetail->totalsales * $this->royaltySettingModel->tax / 100;
        $royaltyDetail->fee = $this->royaltySettingModel->fee;
        $royaltyDetail->totalfee = $royaltyDetail->totalsales * $this->royaltySettingModel->fee / 100;
        $royaltyDetail->invoiceardetailid = $this->invoicedetailModel->id;
        $royaltyDetail->goodsissuedetailid = $this->invoicedetailModel->goodsissuedetailid;
        $royaltyDetail->salesorderdetailid = $this->sodetailModel->id;
        $royaltyDetail->createdAt = date('Y-m-d H:i:s');
        $royaltyDetail->createdBy = Yii::$app->user->identity->userID;
        if (!$royaltyDetail->save()) {
            foreach ($royaltyDetail->errors as $errors) {
                foreach ($errors as $attribute => $error) {
                    throw new Exception('Gagal simpan data detail: ' . $error);
                }
            }
        }
    }
    
    private function insertInvoiceRoyaltyDetail($invoiceRoyalty) {
        $royaltyDetail = new Invoiceroyaltydetail();
        $royaltyDetail->head_id = $invoiceRoyalty->id;
        $royaltyDetail->transdate = $invoiceRoyalty->transdate;
        $royaltyDetail->qty = $this->invoicedetailModel->qty;
        $royaltyDetail->price = $this->invoicedetailModel->price;
        $royaltyDetail->discount = $this->invoicedetailModel->price;
        $royaltyDetail->totaldiscount = $this->invoicedetailModel->totaldiscount;
        $royaltyDetail->vat = $this->invoicedetailModel->vat;
        $royaltyDetail->totalvat = $this->invoicedetailModel->totalvat;
        $royaltyDetail->totalsales = $this->invoicedetailModel->total;
        $royaltyDetail->tax = $this->royaltySettingModel->tax;
        $royaltyDetail->totaltax = $royaltyDetail->totalsales * $this->royaltySettingModel->tax / 100;
        $royaltyDetail->fee = $this->royaltySettingModel->fee;
        $royaltyDetail->totalfee = $royaltyDetail->totalsales * $this->royaltySettingModel->fee / 100;
        $royaltyDetail->invoiceardetailid = $this->invoicedetailModel->id;
        $royaltyDetail->goodsissuedetailid = $this->invoicedetailModel->goodsissuedetailid;
        $royaltyDetail->salesorderdetailid = $this->sodetailModel->id;
        $royaltyDetail->createdAt = date('Y-m-d H:i:s');
        $royaltyDetail->createdBy = Yii::$app->user->identity->userID;
        if (!$royaltyDetail->save()) {
            foreach ($royaltyDetail->errors as $errors) {
                foreach ($errors as $attribute => $error) {
                    throw new Exception('Gagal simpan data detail: ' . $error);
                }
            }
        }
    }
    
    private function calculateTotal() {
        $royaltyModel = Royalty::find()->where([
            'royaltysettingid' => $this->royaltySettingModel->royaltysettingid,
            'addressbookid' => $this->royaltySettingModel->addressbookid,
            'productid' => $this->productid])
            ->one();
        
        if ($royaltyModel) {
            $sumQuery = (new Query())
            ->select([
                'totalqty' => 'SUM(qty)',
                'totaldiscount' => 'SUM(totaldiscount)',
                'totalvat' => 'SUM(totalvat)',
                'totalsales' => 'SUM(totalsales)',
                'totaltax' => 'SUM(totaltax)',
                'totalfee' => 'SUM(totalfee)'
            ])
            ->from(Royaltydetail::tableName())
            ->where(['head_id' => $royaltyModel->id])
            ->one();
            
            $royaltyModel->totalqty = $sumQuery['totalqty'];
            $royaltyModel->totaldiscount = $sumQuery['totaldiscount'];
            $royaltyModel->totalvat = $sumQuery['totalvat'];
            $royaltyModel->totalsales = $sumQuery['totalsales'];
            $royaltyModel->totaltax = $sumQuery['totaltax'];
            $royaltyModel->totalfee = $sumQuery['totalfee'];
            $royaltyModel->updatedAt = date('Y-m-d H:i:s');
            $royaltyModel->updatedBy = Yii::$app->user->identity->userID;
            if (!$royaltyModel->save()) {
                foreach ($royaltyModel->errors as $errors) {
                    foreach ($errors as $attribute => $error) {
                        throw new Exception('Gagal simpan data: ' . $error);
                    }
                }
            }
        }
    }
    
    private function calculateInvoiceTotal() {
        $royaltyModel = Invoiceroyalty::find()->where([
            'royaltysettingid' => $this->royaltySettingModel->royaltysettingid,
            'addressbookid' => $this->royaltySettingModel->addressbookid,
            'productid' => $this->productid])
            ->andWhere('transdate > CURDATE()')
            ->orderBy('transdate DESC')
            ->one();
        
        if ($royaltyModel) {
            $sumQuery = (new Query())
            ->select([
                'totalqty' => 'SUM(qty)',
                'totalfee' => 'SUM(totalfee)'
            ])
            ->from(Invoiceroyaltydetail::tableName())
            ->where(['head_id' => $royaltyModel->id])
            ->one();
            
            $royaltyModel->totalqty = $sumQuery['totalqty'];
            $royaltyModel->amount = $sumQuery['totalfee'];
            $royaltyModel->updatedAt = date('Y-m-d H:i:s');
            $royaltyModel->updatedBy = Yii::$app->user->identity->userID;
            if (!$royaltyModel->save()) {
                foreach ($royaltyModel->errors as $errors) {
                    foreach ($errors as $attribute => $error) {
                        throw new Exception('Gagal simpan data: ' . $error);
                    }
                }
            }
        }
    }
}
