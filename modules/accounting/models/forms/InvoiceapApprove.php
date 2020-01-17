<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\accounting\models\Invoiceap;
use app\modules\accounting\models\Invoiceapdetail;
use app\modules\purchase\models\Purchaseorderdetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approve sales order.
 */
class InvoiceapApprove extends Invoiceap
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appap', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appap');
            if ($nextStatus) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }

                if ($maxStatus == $nextStatus) {
                    $grDetailModel = $this->findGrDetailModel($this->goodsreceiptid);
                    $poDetailModel = $this->findPoDetailModel($this->goodsreceipt->purchaseorderid);
                    if ($grDetailModel) {
                        foreach ($grDetailModel as $detail) {
                            $detail->invqty = $this->getApQty($detail->productid);
                            if (!$detail->save(false)) {
                                $errMsg = 'Kesalahan saat ubah detail data penerimaan barang.';
                                throw new Exception($errMsg);
                            }
                        }
                    }
                    if ($poDetailModel) {
                        foreach ($poDetailModel as $detail) {
                            $detail->invqty = $this->getApQty($detail->productid);
                            if (!$detail->save(false)) {
                                $errMsg = 'Kesalahan saat ubah detail data pembelian barang.';
                                throw new Exception($errMsg);
                            }
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data faktur pembelian. Silahkan ke menu Alur Kerja dan Grup.';
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
    
    protected function findApModelDetail($id) {
        if (($model = Invoiceapdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findGrDetailModel($id) {
        if (($model = Goodsreceiptdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findPoDetailModel($id) {
        if (($model = Purchaseorderdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    private function getApQty($productid) {
        $detailModel = Invoiceapdetail::find()
            ->select([
                'tr_invoiceapdetail.productid',
                'qty' => new \yii\db\Expression("SUM(qty)")
            ])
            ->andWhere(['=', 'tr_invoiceapdetail.productid', $productid])
            ->groupBy('tr_invoiceapdetail.productid')
            ->one();
        
        if ($detailModel) {
            return $detailModel['qty'];
        }
        return null;
    }
}
