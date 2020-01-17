<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\accounting\models\Advancepayment;
use app\modules\accounting\models\Invoicear;
use app\modules\accounting\models\Salespayment;
use app\modules\admin\models\Wfgroup;

class SalespaymentApprove extends Salespayment
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appsp', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appsp');
            if ($nextStatus) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }

                if ($maxStatus == $nextStatus) {
                    $invoicearModel = Invoicear::findOne($this->invoicearid);
                    if ($invoicearModel) {
                        $invoicearModel->payamount = $this->payamount + $this->advanceamount + $invoicearModel->payamount;
                        if (!$invoicearModel->save(false)) {
                            $errMsg = 'Kesalahan saat ubah data faktur penjualan.';
                            throw new Exception($errMsg);
                        }
                        
                        if ($invoicearModel->payamount > $invoicearModel->aramount) {
                            $overPay = number_format(($invoicearModel->payamount - $invoicearModel->aramount), 0, ',', '.');
                            $errMsg = "Kelebihan bayar senilai $overPay!";
                            throw new Exception($errMsg);
                        }
                    }
                    if ($this->advancepaymentid) {
                        $advanceModel = Advancepayment::findOne($this->advancepaymentid);
                        $advanceModel->isUsed = 1;
                        $advanceModel->updatedAt = date('Y-m-d H:i:s');
                        $advanceModel->updatedBy = Yii::$app->user->identity->userID;
                        if (!$advanceModel->save()) {
                            $errMsg = 'Kesalahan saat ubah status data.';
                            throw new Exception($errMsg);
                        }
                    }
                    $this->paidamount = $invoicearModel->payamount;
                    $this->updatedAt = date('Y-m-d H:i:s');
                    $this->updatedBy = Yii::$app->user->identity->userID;
                    if (!$this->save()) {
                        $errMsg = 'Kesalahan saat ubah status data.';
                        throw new Exception($errMsg);
                    }
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
