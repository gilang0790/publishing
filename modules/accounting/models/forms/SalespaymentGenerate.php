<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\Advancepayment;
use app\modules\accounting\models\Invoicear;
use app\modules\accounting\models\Salespayment;
use app\modules\admin\models\Wfgroup;

class SalespaymentGenerate extends Salespayment
{
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('inssp');
            $umMaxStatus = Wfgroup::getMaxStatus('appum');
            $invoicearModel = Invoicear::findOne($qq);
            if ($status) {
                if ($invoicearModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Sales Payment', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }
                    
                    // Cari uang muka untuk pesanan penjualan
                    $advanceModel = Advancepayment::find()->where([
                        'salesorderid' => $invoicearModel->goodsissue->salesorderid,
                        'status' => $umMaxStatus,
                        'isUsed' => 0])->one();

                    // simpan data sales payment
                    $this->sptransnum = $newTransNum;
                    $this->sptransdate = date('Y-m-d');
                    $this->plantid = $invoicearModel->plantid;
                    $this->addressbookid = $invoicearModel->addressbookid;
                    $this->invoicearid = $invoicearModel->id;
                    $this->aramount = $invoicearModel->aramount;
                    $this->paidamount = $invoicearModel->payamount;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if ($advanceModel) {
                        $this->advancepaymentid = $advanceModel->id;
                        $this->advanceamount = $advanceModel->amount;
                    }
                    $this->payamount = 0;
                    $this->stringPayamount = '0';
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen faktur penjualan gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data penerimaan penjualan. Silahkan ke menu Alur Kerja dan Grup.';
                throw new Exception($errMsg);
            }
            
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            $errMsg = $ex->getMessage();
            return false;
        }
    }
}
