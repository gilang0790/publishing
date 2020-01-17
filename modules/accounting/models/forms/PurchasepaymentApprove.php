<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\accounting\models\Invoiceap;
use app\modules\accounting\models\Purchasepayment;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approve purchase order.
 */
class PurchasepaymentApprove extends Purchasepayment
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('apppp', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('apppp');
            if ($nextStatus) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }

                if ($maxStatus == $nextStatus) {
                    $invoiceapModel = Invoiceap::findOne($this->invoiceapid);
                    if ($invoiceapModel) {
                        $invoiceapModel->payamount = $this->payamount;
                        if (!$invoiceapModel->save(false)) {
                            $errMsg = 'Kesalahan saat ubah data faktur pembelian.';
                            throw new Exception($errMsg);
                        }
                        
                        if ($invoiceapModel->payamount > $invoiceapModel->apamount) {
                            $extraAmount = $invoiceapModel->payamount - $invoiceapModel->apamount;
                            $errMsg = "Kelebihan bayar senilai $extraAmount!";
                            throw new Exception($errMsg);
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data pembayaran pembelian. Silahkan ke menu Alur Kerja dan Grup.';
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
