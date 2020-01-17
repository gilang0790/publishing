<?php

namespace app\modules\order\models\forms;

use app\modules\order\models\Salesorderinfo;
use app\modules\order\models\Salesorderinfodetail;
use app\modules\order\models\Salesorder;
use app\modules\order\models\Salesorderdetail;
use Exception;
use Yii;

class SalesorderinfoGenerate extends Salesorderinfo
{
    
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Salesorderinfo::STATUS_ACTIVE;
            $salesorderModel = Salesorder::findOne($qq);
            if ($salesorderModel) {
                $this->plantid = $salesorderModel->plantid;
                $this->salesorderid = $salesorderModel->id;
                $this->status = $status;
                $this->createdAt = date('Y-m-d H:i:s');
                $this->createdBy = Yii::$app->user->identity->userID;
                if (!$this->save(false)) {
                    $errMsg = 'Kesalahan saat simpan data.';
                    throw new Exception($errMsg);
                }

                // simpan goods issue detail
                $details = $this->findModelDetail($salesorderModel->id);
                if ($details) {
                    foreach ($details as $detail) {
                        $gidetailModel = new Salesorderinfodetail();
                        $gidetailModel->head_id = $this->id;
                        $gidetailModel->productid = $detail->productid;
                        $gidetailModel->unitofmeasureid = $detail->unitofmeasureid;
                        $gidetailModel->qty = $detail->qty;
                        $gidetailModel->addressbookid = $salesorderModel->addressbookid;
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
