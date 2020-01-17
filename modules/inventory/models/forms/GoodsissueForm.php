<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Goodsissue;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;


class GoodsissueForm extends Goodsissue
{
    
    public function saveModel(&$errMsg, $details, $deletedIDs=null) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->shippingcost) {
                $rawShippingCost = str_replace(".", "", $this->shippingcost);
                $this->shippingcost = $rawShippingCost;
            }
            $this->updatedAt = date('Y-m-d H:i:s');
            $this->updatedBy = Yii::$app->user->identity->userID;
            if (!$this->save(false)) {
                $errMsg = 'Kesalahan saat simpan data.';
                throw new Exception($errMsg);
            }
            
            if (!empty($deletedIDs)) {
                Goodsissuedetail::deleteAll(['id' => $deletedIDs]);
            }
            
            foreach ($details as $detail) {
                $productname = Product::getProductName($detail->productid);
                if (($detail->qty + $detail->sodetail->giqty) > $detail->sodetail->qty) {
                    $errMsg = "Jumlah $productname yang dikeluarkan melebihi jumlah pesanan penjualan.";
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
