<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\accounting\models\Salespayment;

class SalespaymentForm extends Salespayment
{
    public function saveModel(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Cek kelebihan pembayaran
            if ($this->payamount > ($this->aramount - $this->paidamount - $this->advanceamount)) {
                $overPay = number_format(($this->payamount - ($this->aramount - $this->paidamount - $this->advanceamount)), 0, ',', '.');
                $errMsg = "Peringatan! Kelebihan pembayaran senilai $overPay.";
                throw new Exception($errMsg);
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
