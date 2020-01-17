<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;

class GoodsreceiptForm extends Goodsreceipt
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
                Goodsreceiptdetail::deleteAll(['id' => $deletedIDs]);
            }
            
            foreach ($details as $detail) {
                $productname = Product::getProductName($detail->productid);
                if (($detail->qty + $detail->podetail->grqty) > $detail->podetail->qty) {
                    $errMsg = "Jumlah $productname yang diterima melebihi jumlah pembelian.";
                    throw new Exception($errMsg);
                }
                $detail->head_id = $this->id;
                $detail->unitofmeasureid = ProductSearchModel::getUomID($detail->productid);
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
}
