<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\Invoiceap;
use app\modules\accounting\models\Purchasepayment;
use app\modules\admin\models\Wfgroup;

class PurchasepaymentGenerate extends Purchasepayment
{
    
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('inspp');
            $invoiceapModel = Invoiceap::findOne($qq);
            if ($status) {
                if ($invoiceapModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Purchase Payment', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }

                    // simpan data head invoice ar
                    $this->pptransnum = $newTransNum;
                    $this->pptransdate = date('Y-m-d');
                    $this->plantid = $invoiceapModel->plantid;
                    $this->addressbookid = $invoiceapModel->addressbookid;
                    $this->invoiceapid = $invoiceapModel->id;
                    $this->apamount = $invoiceapModel->apamount - $invoiceapModel->payamount;
                    $this->payamount = $invoiceapModel->payamount;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen faktur pembelian gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data pembayaran pembelian. Silahkan ke menu Alur Kerja dan Grup.';
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
