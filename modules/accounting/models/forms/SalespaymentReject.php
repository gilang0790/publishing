<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\accounting\models\Salespayment;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for reject sales order.
 */
class SalespaymentReject extends Salespayment
{
    
    public function reject(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('rejsp', $this->status);
            if ($nextStatus || $nextStatus == 0) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
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
