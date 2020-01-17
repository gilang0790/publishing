<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\Advancepayment;
use app\modules\common\models\Paymentmethod;
use app\modules\order\models\Salesorder;
use app\modules\admin\models\Wfgroup;

class AdvancepaymentForm extends Advancepayment
{
    public function saveModel(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        $newTrans = $this->isNewRecord;
        $soTotal = 0;
        if ($this->salesorderid) {
            $salesOrderModel = Salesorder::findOne($this->salesorderid);
            $this->plantid = $salesOrderModel->plantid;
            $soTotal = $salesOrderModel->grandtotal;
        }
        try {
            if ($newTrans) {
                $status = Wfgroup::getMaxStatus('insum');
                if (!$status) {
                    $errMsg = 'Anda belum memiliki akses untuk buat/ubah data uang muka penjualan. Silahkan ke menu Alur Kerja dan Grup.';
                    throw new Exception($errMsg);
                }
                $newTransNum = AppHelper::createNewTransactionNumber('Advance Payment', $this->umtransdate);
                if ($newTransNum == "") {
                    $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                    throw new Exception($errMsg);
                }
                $this->umtransnum = $newTransNum;
                $this->status = $status;
            }
            
            $stringAmount = str_replace(".", "", $this->stringAmount);
            if ($this->salesorder->paymentmethodid == Paymentmethod::CASH) {
                if ($stringAmount !== $soTotal) {
                    $errMsg = 'Jumlah uang muka penjualan tidak sama dengan total pesanan penjualan.';
                    throw new Exception($errMsg);
                }
            } else {
                if ($stringAmount > $soTotal) {
                    $errMsg = 'Jumlah uang muka penjualan melebihi total pesanan penjualan.';
                    throw new Exception($errMsg);
                }
            }
            
            $this->updatedAt = date('Y-m-d H:i:s');
            $this->updatedBy = Yii::$app->user->identity->userID;
            if (!$this->save(false)) {
                $errMsg = 'Kesalahan saat simpan data.';
                throw new Exception($errMsg);
            }
            
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            Yii::error($ex);
            $transaction->rollBack();
            return false;
        }
    }
}
