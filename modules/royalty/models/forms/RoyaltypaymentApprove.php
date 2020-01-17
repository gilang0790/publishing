<?php

namespace app\modules\royalty\models\forms;

use Exception;
use Yii;
use app\modules\royalty\models\Advanceroyalty;
use app\modules\royalty\models\Invoiceroyalty;
use app\modules\royalty\models\Royaltypayment;
use app\modules\admin\models\Wfgroup;

class RoyaltypaymentApprove extends Royaltypayment
{
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('apprp', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('apprp');
            if ($nextStatus) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }

                if ($maxStatus == $nextStatus) {
                    $invoicerapModel = Invoiceroyalty::findOne($this->invoiceroyaltyid);
                    if ($invoicerapModel) {
                        $invoicerapModel->payamount = $this->payamount + $this->advanceamount + $invoicerapModel->payamount;
                        if (!$invoicerapModel->save(false)) {
                            $errMsg = 'Kesalahan saat ubah data faktur pembelian.';
                            throw new Exception($errMsg);
                        }
                        
                        if ($invoicerapModel->payamount > $invoicerapModel->amount) {
                            $extraAmount = $invoicerapModel->payamount - $invoicerapModel->amount;
                            $errMsg = "Kelebihan bayar senilai $extraAmount!";
                            throw new Exception($errMsg);
                        }
                    }
                    if ($this->advanceroyaltyid) {
                        $advanceModel = Advanceroyalty::findOne($this->advanceroyaltyid);
                        $advanceModel->isUsed = 1;
                        $advanceModel->updatedAt = date('Y-m-d H:i:s');
                        $advanceModel->updatedBy = Yii::$app->user->identity->userID;
                        if (!$advanceModel->save()) {
                            $errMsg = 'Kesalahan saat ubah status data.';
                            throw new Exception($errMsg);
                        }
                    }
                    $this->paidamount = $invoicerapModel->payamount;
                    $this->updatedAt = date('Y-m-d H:i:s');
                    $this->updatedBy = Yii::$app->user->identity->userID;
                    if (!$this->save()) {
                        $errMsg = 'Kesalahan saat ubah status data.';
                        throw new Exception($errMsg);
                    }
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
