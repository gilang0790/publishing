<?php

namespace app\modules\royalty\models\forms;

use Exception;
use Yii;
use app\modules\royalty\models\Advanceroyalty;
use app\modules\admin\models\Wfgroup;

class AdvanceroyaltyReject extends Advanceroyalty
{
    
    public function reject(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('rejumr', $this->status);
            if ($nextStatus || $nextStatus == 0) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data uang muka royalti. Silahkan ke menu Alur Kerja dan Grup.';
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
