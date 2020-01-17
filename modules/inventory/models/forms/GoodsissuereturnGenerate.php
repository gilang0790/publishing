<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\inventory\models\Goodsissuereturn;
use app\modules\inventory\models\Goodsissuedetailreturn;
use app\modules\inventory\models\Goodsissue;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\admin\models\Wfgroup;


class GoodsissuereturnGenerate extends Goodsissuereturn
{    
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('insgir');
            $goodsIssueModel = Goodsissue::findOne($qq);
            if ($status) {
                if ($goodsIssueModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Goods Issue Return', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }

                    // simpan data head goods issue return
                    $this->girtransnum = $newTransNum;
                    $this->girtransdate = date('Y-m-d');
                    $this->slocid = $goodsIssueModel->slocid;
                    $this->goodsissueid = $goodsIssueModel->id;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }

                    // simpan goods issue return detail
                    $details = $this->findModelDetail($goodsIssueModel->id);
                    if ($details) {
                        foreach ($details as $detail) {
                            $gidetailModel = new Goodsissuedetailreturn();
                            $gidetailModel->head_id = $this->id;
                            $gidetailModel->productid = $detail->productid;
                            $gidetailModel->unitofmeasureid = $detail->unitofmeasureid;
                            $gidetailModel->qty = $detail->qty - $detail->retqty;
                            $gidetailModel->storagebinid = $detail->storagebinid;
                            $gidetailModel->goodsissuedetailid = $detail->id;
                            $gidetailModel->soqty = $detail->soqty;
                            $gidetailModel->giqty = $detail->qty;
                            $gidetailModel->invqty = $detail->invqty;
                            if (!$gidetailModel->save(false)) {
                                $errMsg = 'Kesalahan saat simpan detail transaksi.';
                                throw new Exception($errMsg);
                            }
                        }
                    } else {
                        $errMsg = 'Detail transaksi pengeluaran barang gagal diakses.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen pengeluaran barang gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data retur penjualan. Silahkan ke menu Alur Kerja dan Grup.';
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
        $listOutstanding = Goodsissuedetail::getReturnDetail($id);
        if (($model = Goodsissuedetail::find()->where("id IN $listOutstanding AND head_id = :head_id", [':head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
