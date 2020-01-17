<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\accounting\models\Purchasepayment;

class PurchasepaymentForm extends Purchasepayment
{
    public function saveModel(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
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
