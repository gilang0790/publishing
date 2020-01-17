<?php

namespace app\modules\royalty\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\royalty\models\Advanceroyalty;
use app\modules\admin\models\Wfgroup;

class AdvanceroyaltyForm extends Advanceroyalty
{
    public function saveModel(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        $newTrans = $this->isNewRecord;
        try {
            if ($newTrans) {
                $status = Wfgroup::getMaxStatus('insumr');
                if (!$status) {
                    $errMsg = 'Anda belum memiliki akses untuk buat/ubah data uang muka royalti. Silahkan ke menu Alur Kerja dan Grup.';
                    throw new Exception($errMsg);
                }
                $newTransNum = AppHelper::createNewTransactionNumber('Advance Royalty', $this->umrtransdate);
                if ($newTransNum == "") {
                    $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                    throw new Exception($errMsg);
                }
                $this->umrtransnum = $newTransNum;
                $this->status = $status;
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
