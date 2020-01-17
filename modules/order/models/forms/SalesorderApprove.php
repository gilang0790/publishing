<?php

namespace app\modules\order\models\forms;

use Exception;
use Yii;
use app\modules\common\models\Paymentmethod;
use app\modules\order\models\Salesorder;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approve sales order.
 */
class SalesorderApprove extends Salesorder
{
    public function approve(&$errMsg, &$successMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appso', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appso');
            if ($nextStatus) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }
                
                if ($maxStatus == $nextStatus) {
                    if ($this->paymentmethodid == Paymentmethod::CASH) {
                        $successMsg = ". Silahkan input pembayaran di menu Uang Muka Penjualan dan keluarkan barang di menu Pengeluaran Barang.";
                    }
                    if ($this->paymentmethodid == Paymentmethod::CREDIT) {
                        $successMsg = ". Silahkan keluarkan barang di menu Pengeluaran Barang.";
                    }
                    if ($this->paymentmethodid == Paymentmethod::NON_SALES) {
                        $successMsg = ". Silahkan keluarkan barang di menu Pengeluaran Barang.";
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data penjualan. Silahkan ke menu Alur Kerja dan Grup.';
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
