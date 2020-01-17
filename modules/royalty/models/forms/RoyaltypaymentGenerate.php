<?php

namespace app\modules\royalty\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\royalty\models\Advanceroyalty;
use app\modules\royalty\models\Invoiceroyalty;
use app\modules\royalty\models\Royaltypayment;
use app\modules\admin\models\Wfgroup;

class RoyaltypaymentGenerate extends Royaltypayment
{
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('insrp');
            $umrMaxStatus = Wfgroup::getMaxStatus('appumr');
            $invoicerapModel = Invoiceroyalty::findOne($qq);
            if ($status) {
                if ($invoicerapModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Royalty Payment', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }
                    
                    // Cari uang muka
                    $advanceModel = Advanceroyalty::find()->where([
                        'addressbookid' => $invoicerapModel->addressbookid,
                        'status' => $umrMaxStatus,
                        'isUsed' => 0])->one();

                    // simpan data
                    $this->rptransnum = $newTransNum;
                    $this->rptransdate = date('Y-m-d');
                    $this->plantid = $invoicerapModel->plantid;
                    $this->invoiceroyaltyid = $invoicerapModel->id;
                    $this->invoiceamount = $invoicerapModel->amount - $invoicerapModel->payamount;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if ($advanceModel) {
                        $this->advanceroyaltyid = $advanceModel->id;
                        $this->advanceamount = $advanceModel->amount;
                    }
                    $this->stringPayamount = '0';
                    $this->payamount = 0;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen faktur royalty gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data pembayaran royalti. Silahkan ke menu Alur Kerja dan Grup.';
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
