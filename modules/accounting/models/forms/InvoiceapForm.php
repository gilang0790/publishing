<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\accounting\models\Invoiceap;
use app\modules\accounting\models\Invoiceapdetail;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;

/**
 * This is the model class for upload product.
 *
 * @property string $fileUpload
 */
class InvoiceapForm extends Invoiceap
{
    public function saveModel(&$errMsg, $details, $deletedIDs=null) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            
            if (!empty($deletedIDs)) {
                Invoiceapdetail::deleteAll(['id' => $deletedIDs]);
            }
            
            $apAmount = 0;
            foreach ($details as $detail) {
                $grdetailModel = $this->getGrdetail($this->goodsreceiptid, $detail->productid);
                $productname = Product::getProductName($detail->productid);
                if ($this->exceedIssue($this->goodsreceiptid, $detail->productid, $detail->qty)) {
                    $errMsg = "Jumlah $productname yang ditagih melebihi jumlah pengeluaran barang.";
                    throw new Exception($errMsg);
                }
                $detail->head_id = $this->id;
                $detail->unitofmeasureid = ProductSearchModel::getUomID($detail->productid);
                $detail->goodsreceiptdetailid = $grdetailModel->id;
                $detail->poqty = $grdetailModel->poqty;
                $detail->grqty = $grdetailModel->qty;
                $apAmount += $detail->total;
                $detail->total = $detail->qty * $detail->price;
                if (!$detail->save(false)) {
                    $errMsg = 'Kesalahan saat simpan detail transaksi.';
                    throw new Exception($errMsg);
                }
            }
            $this->apamount = $apAmount;
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
    
    protected function getGrdetail($id, $productid) {
        $grdetail = Goodsreceiptdetail::find()
            ->andWhere(['=', 'tr_goodsreceiptdetail.head_id', $id])
            ->andWhere(['=', 'tr_goodsreceiptdetail.productid', $productid])
            ->one();
        
        if ($grdetail) {
            return $grdetail;
        }
        return NULL;
    }
}
