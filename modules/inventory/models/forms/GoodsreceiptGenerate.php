<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\purchase\models\Purchaseorder;
use app\modules\purchase\models\Purchaseorderdetail;
use app\modules\admin\models\Wfgroup;


class GoodsreceiptGenerate extends Goodsreceipt
{
    
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('insgr');
            $purchaseorderModel = Purchaseorder::findOne($qq);
            if ($status) {
                if ($purchaseorderModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Goods Receipt', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }

                    // simpan data head goods receipt
                    $this->grtransnum = $newTransNum;
                    $this->grtransdate = date('Y-m-d');
                    $this->purchaseorderid = $purchaseorderModel->id;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }

                    // Ubah isgenerated menjadi true
                    $purchaseorderModel->isgenerated = 1;
                    if (!$purchaseorderModel->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data pembelian.';
                        throw new Exception($errMsg);
                    }

                    // simpan goods receipt detail
                    $details = $this->findModelDetail($purchaseorderModel->id);
                    if ($details) {
                        foreach ($details as $detail) {
                            $grdetailModel = new Goodsreceiptdetail();
                            $grdetailModel->head_id = $this->id;
                            $grdetailModel->productid = $detail->productid;
                            $grdetailModel->unitofmeasureid = $detail->unitofmeasureid;
                            $grdetailModel->qty = $detail->qty - $detail->grqty;
                            $grdetailModel->purchaseorderdetailid = $detail->id;
                            $grdetailModel->poqty = $detail->qty;
                            if (!$grdetailModel->save(false)) {
                                $errMsg = 'Kesalahan saat simpan detail transaksi.';
                                throw new Exception($errMsg);
                            }
                        }
                    } else {
                        $errMsg = 'Detail transaksi pembelian gagal diakses.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen pembelian gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data pembelian. Silahkan ke menu Alur Kerja dan Grup.';
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

    protected function findModelDetail($id) {
        if (($model = Purchaseorderdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
