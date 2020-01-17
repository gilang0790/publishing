<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\inventory\models\Goodsreceiptreturn;
use app\modules\inventory\models\Goodsreceiptdetailreturn;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\admin\models\Wfgroup;


class GoodsreceiptreturnGenerate extends Goodsreceiptreturn
{    
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('insgrr');
            $goodsReceiptModel = Goodsreceipt::findOne($qq);
            if ($status) {
                if ($goodsReceiptModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Goods Receipt Return', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }

                    // simpan data head goods receipt return
                    $this->grrtransnum = $newTransNum;
                    $this->grrtransdate = date('Y-m-d');
                    $this->slocid = $goodsReceiptModel->slocid;
                    $this->goodsreceiptid = $goodsReceiptModel->id;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }

                    // simpan goods receipt return detail
                    $details = $this->findModelDetail($goodsReceiptModel->id);
                    if ($details) {
                        foreach ($details as $detail) {
                            $gidetailModel = new Goodsreceiptdetailreturn();
                            $gidetailModel->head_id = $this->id;
                            $gidetailModel->productid = $detail->productid;
                            $gidetailModel->unitofmeasureid = $detail->unitofmeasureid;
                            $gidetailModel->qty = $detail->qty;
                            $gidetailModel->storagebinid = $detail->storagebinid;
                            $gidetailModel->goodsreceiptdetailid = $detail->id;
                            $gidetailModel->poqty = $detail->poqty;
                            $gidetailModel->grqty = $detail->qty;
                            $gidetailModel->invqty = $detail->invqty;
                            if (!$gidetailModel->save(false)) {
                                $errMsg = 'Kesalahan saat simpan detail transaksi.';
                                throw new Exception($errMsg);
                            }
                        }
                    } else {
                        $errMsg = 'Detail transaksi penerimaan barang gagal diakses.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen penerimaan barang gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data retur pembelian. Silahkan ke menu Alur Kerja dan Grup.';
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
        if (($model = Goodsreceiptdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
