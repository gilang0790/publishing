<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\accounting\models\Invoicear;
use app\modules\accounting\models\Invoiceardetail;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;

/**
 * This is the model class for upload product.
 *
 * @property string $fileUpload
 */
class InvoicearForm extends Invoicear
{
    public function saveModel(&$errMsg, $details, $deletedIDs=null) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!empty($deletedIDs)) {
                Invoiceardetail::deleteAll(['id' => $deletedIDs]);
            }
            
            $arAmount = 0;
            foreach ($details as $detail) {
                $gidetailModel = $this->getGidetail($this->goodsissueid, $detail->productid);
                $productname = Product::getProductName($detail->productid);
                if ($this->exceedIssue($this->goodsissueid, $detail->productid, $detail->qty)) {
                    $errMsg = "Jumlah $productname yang ditagih melebihi jumlah pengeluaran barang.";
                    throw new Exception($errMsg);
                }
                $detail->head_id = $this->id;
                $detail->unitofmeasureid = ProductSearchModel::getUomID($detail->productid);
                $detail->goodsissuedetailid = $gidetailModel->id;
                $detail->totalvat = $detail->qty * $detail->price * $detail->vat / 100;
                $detail->totaldiscount = $detail->qty * $detail->price * $detail->discount / 100;
                $detail->soqty = $gidetailModel->soqty;
                $detail->giqty = $gidetailModel->qty;
                $detail->total = $this->getDetailTotal($detail->qty, $detail->price, $detail->vat, $detail->discount);
                $arAmount += $detail->total;
                if (!$detail->save(false)) {
                    $errMsg = 'Kesalahan saat simpan detail transaksi.';
                    throw new Exception($errMsg);
                }
            }
            $this->aramount = $arAmount;
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
    
    protected function getDetailTotal($qty, $price, $vat, $discount) {
        $result = ($qty * $price) +
                    ($qty * $price * $vat / 100) - 
                    ($qty * $price * $discount / 100);
        
        return $result;
    }
    
    protected function exceedIssue($id, $productid, $qty) {
        $result = false;
        $gidetail = Goodsissuedetail::find()
            ->andWhere(['=', 'tr_goodsissuedetail.head_id', $id])
            ->andWhere(['=', 'tr_goodsissuedetail.productid', $productid])
            ->andWhere("($qty + COALESCE(tr_goodsissuedetail.invqty, 0) + COALESCE(tr_goodsissuedetail.retqty, 0)) > tr_goodsissuedetail.qty")
            ->one();
        
        if ($gidetail) {
            $result = true;
        }
        
        return $result;
    }
    
    protected function getGidetail($id, $productid) {
        $gidetail = Goodsissuedetail::find()
            ->andWhere(['=', 'tr_goodsissuedetail.head_id', $id])
            ->andWhere(['=', 'tr_goodsissuedetail.productid', $productid])
            ->one();
        
        if ($gidetail) {
            return $gidetail;
        }
        return NULL;
    }
}
