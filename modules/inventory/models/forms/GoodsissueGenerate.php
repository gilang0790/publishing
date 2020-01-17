<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\inventory\models\Goodsissue;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\order\models\Salesorder;
use app\modules\order\models\Salesorderdetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for upload product.
 *
 * @property string $fileUpload
 */
class GoodsissueGenerate extends Goodsissue
{
    
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('insgi');
            $salesorderModel = Salesorder::findOne($qq);
            if ($status) {
                if ($salesorderModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Goods Issue', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }

                    // simpan data head goods issue
                    $this->gitransnum = $newTransNum;
                    $this->gitransdate = date('Y-m-d');
                    $this->salesorderid = $salesorderModel->id;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }

                    // Ubah isgenerated menjadi true
                    $salesorderModel->isgenerated = 1;
                    if (!$salesorderModel->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data penjualan.';
                        throw new Exception($errMsg);
                    }

                    // simpan goods issue detail
                    $details = $this->findModelDetail($salesorderModel->id);
                    if ($details) {
                        foreach ($details as $detail) {
                            $gidetailModel = new Goodsissuedetail();
                            $gidetailModel->head_id = $this->id;
                            $gidetailModel->productid = $detail->productid;
                            $gidetailModel->unitofmeasureid = $detail->unitofmeasureid;
                            $gidetailModel->qty = $detail->qty;
                            $gidetailModel->salesorderdetailid = $detail->id;
                            $gidetailModel->soqty = $detail->qty;
                            if (!$gidetailModel->save(false)) {
                                $errMsg = 'Kesalahan saat simpan detail transaksi.';
                                throw new Exception($errMsg);
                            }
                        }
                    } else {
                        $errMsg = 'Detail transaksi penjualan gagal diakses.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen penjualan gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data pengeluaran barang. Silahkan ke menu Alur Kerja dan Grup.';
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
        if (($model = Salesorderdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
