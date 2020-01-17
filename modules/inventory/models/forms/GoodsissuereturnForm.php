<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\inventory\models\Goodsissuereturn;
use app\modules\inventory\models\Goodsissuedetailreturn;


class GoodsissuereturnForm extends Goodsissuereturn
{   
    public function saveModel(&$errMsg, $details, $deletedIDs=null) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->updatedAt = date('Y-m-d H:i:s');
            $this->updatedBy = Yii::$app->user->identity->userID;
            if (!$this->save(false)) {
                $errMsg = 'Kesalahan saat simpan data.';
                throw new Exception($errMsg);
            }
            
            if (!empty($deletedIDs)) {
                Goodsissuedetailreturn::deleteAll(['id' => $deletedIDs]);
            }

            foreach ($details as $detail) {
                $gidetailModel = $this->getGidetail($this->goodsissueid, $detail->productid);
                $productname = Product::getProductName($detail->productid);
                if ($this->exceedIssue($this->goodsissueid, $detail->productid, $detail->qty)) {
                    $errMsg = "Jumlah $productname yang diretur melebihi jumlah barang yang telah tertagih dan diretur.";
                    throw new Exception($errMsg);
                }
                $detail->head_id = $this->id;
                $detail->unitofmeasureid = ProductSearchModel::getUomID($detail->productid);
                $detail->goodsissuedetailid = $gidetailModel->id;
                $detail->soqty = $gidetailModel->soqty;
                $detail->giqty = $gidetailModel->qty;
                $detail->invqty = $gidetailModel->invqty;
                if (!$detail->save(false)) {
                    $errMsg = 'Kesalahan saat simpan detail transaksi.';
                    throw new Exception($errMsg);
                }
            }
            
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            Yii::error($ex);
            $transaction->rollBack();
            return false;
        }
    }
    
    protected function exceedIssue($id, $productid, $qty) {
        $result = false;
        $gidetail = Goodsissuedetail::find()
            ->andWhere(['=', 'tr_goodsissuedetail.head_id', $id])
            ->andWhere(['=', 'tr_goodsissuedetail.productid', $productid])
            ->andWhere("($qty + COALESCE(tr_goodsissuedetail.retqty, 0)) > tr_goodsissuedetail.qty")
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
