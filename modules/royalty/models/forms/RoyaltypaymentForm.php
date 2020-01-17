<?php

namespace app\modules\royalty\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\royalty\models\Royaltypayment;
use app\modules\admin\models\Wfgroup;

class RoyaltypaymentForm extends Royaltypayment
{
    public function saveModel(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        $newTrans = $this->isNewRecord;
        try {
            if ($newTrans) {
                $status = Wfgroup::getMaxStatus('insrp');
                if (!$status) {
                    $errMsg = 'Anda belum memiliki akses untuk buat/ubah data pembayaran royalti. Silahkan ke menu Alur Kerja dan Grup.';
                    throw new Exception($errMsg);
                }
                $newTransNum = AppHelper::createNewTransactionNumber('Royalty Payment', $this->rptransdate);
                if ($newTransNum == "") {
                    $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                    throw new Exception($errMsg);
                }
                $this->rptransnum = $newTransNum;
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
