<?php

namespace app\modules\order\models\forms;

use Exception;
use Yii;
use app\modules\common\models\Customer;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\order\models\Salesorderinfo;
use app\modules\order\models\Salesorderdetail;
use app\modules\order\models\Salesorderinfodetail;

/**
 * This is the model class for create and update sales order.
 */
class SalesorderinfoForm extends Salesorderinfo
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
                Salesorderinfodetail::deleteAll(['id' => $deletedIDs]);
            }
            
            foreach ($details as $detail) {
                $detail->head_id = $this->id;
                $detail->unitofmeasureid = ProductSearchModel::getUomID($detail->productid);
                $detail->soqty = $detail->qty;
                $detail->giqty = $detail->qty;
                if (!$detail->save(false)) {
                    $errMsg = 'Kesalahan saat simpan detail transaksi.';
                    throw new Exception($errMsg);
                }
            }
            
            $checkRedundantModelDetail = Salesorderinfodetail::find()->where(['=', 'tr_salesorderinfodetail.head_id', $this->id])->all();
            foreach ($checkRedundantModelDetail as $detail) {
                $productname = Product::getProductName($detail->productid);
                $outletname = Customer::getFullName($detail->addressbookid);
                if ($this->checkRedundant($detail->productid, $detail->addressbookid)) {
                    $errMsg = "$productname di $outletname telah terdaftar sebelumnya.";
                    throw new Exception($errMsg);
                }
            }
            
            $detailModel = Salesorderinfodetail::find()
                ->select([
                    'tr_salesorderinfodetail.productid',
                    'qty' => new \yii\db\Expression("SUM(qty)")
                ])
                ->andWhere(['=', 'tr_salesorderinfodetail.head_id', $this->id])
                ->groupBy('tr_salesorderinfodetail.productid')
                ->all();
            
            foreach ($detailModel as $data) {
                $productname = Product::getProductName($data['productid']);
                $soQty = $this->getQty($data['productid']);
                if ($data['qty'] > $soQty) {
                    $errMsg = "$productname melebihi jumlah yang tercatat di data penjualan.";
                    throw new Exception($errMsg);
                }
                if ($data['qty'] < $soQty) {
                    $errMsg = "$productname kurang dari jumlah yang tercatat di data penjualan.";
                    throw new Exception($errMsg);
                }
                if ($this->getIssueQty($data['productid'])) {
                    $giQty = $this->getIssueQty($data['productid']);
                    if ($data['qty'] > $giQty) {
                        $errMsg = "$productname melebihi jumlah yang tercatat di data pengeluaran barang.";
                        throw new Exception($errMsg);
                    }
                    if ($data['qty'] < $giQty) {
                        $errMsg = "$productname kurang dari jumlah yang tercatat di data pengeluaran barang.";
                        throw new Exception($errMsg);
                    }
                }
            }
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            $errMsg = $ex->getMessage();
            return false;
        }
    }
    
    private function getQty($productid) {
        $detailModel = Salesorderdetail::find()
            ->select([
                'tr_salesorderdetail.productid',
                'qty' => new \yii\db\Expression("SUM(qty)")
            ])
            ->andWhere(['=', 'tr_salesorderdetail.head_id', $this->salesorderid])
            ->andWhere(['=', 'tr_salesorderdetail.productid', $productid])
            ->groupBy('tr_salesorderdetail.productid')
            ->one();
        
        return $detailModel['qty'];
    }
    
    private function getIssueQty($productid) {
        $detailModel = Goodsissuedetail::find()
            ->innerJoinWith('goodsissue')
            ->select([
                'tr_goodsissuedetail.productid',
                'qty' => new \yii\db\Expression("SUM(qty)")
            ])
            ->andWhere(['=', 'tr_goodsissue.salesorderid', $this->salesorderid])
            ->andWhere(['=', 'tr_goodsissuedetail.productid', $productid])
            ->groupBy('tr_goodsissuedetail.productid')
            ->one();
        
        if ($detailModel) {
            return $detailModel['qty'];
        }
        return null;
    }
    
    private function checkRedundant($productid, $addressbookid) {
        $model = Salesorderinfodetail::find()
            ->andWhere(['tr_salesorderinfodetail.productid' => $productid, 'tr_salesorderinfodetail.addressbookid' => $addressbookid])
            ->andWhere(['=', 'tr_salesorderinfodetail.head_id', $this->salesorderid])
            ->count();
        
        if ($model > 1) {
            return true;
        } else {
            return false;
        }
    }
}
