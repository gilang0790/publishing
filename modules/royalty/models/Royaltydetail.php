<?php

namespace app\modules\royalty\models;

use Yii;

/**
 * This is the model class for table "tr_royaltydetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $addressbookid
 * @property int $productid
 * @property string $qty
 * @property string $price
 * @property string $discount
 * @property string $totaldiscount
 * @property string $vat
 * @property string $totalvat
 * @property string $totalsales
 * @property string $tax
 * @property string $totaltax
 * @property string $fee
 * @property string $totalfee
 * @property int $invoiceardetailid
 * @property int $goodsissuedetailid
 * @property int $salesorderdetailid
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 */
class Royaltydetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_royaltydetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'createdAt', 'createdBy'], 'required'],
            [['head_id', 'addressbookid', 'productid', 'invoiceardetailid', 'goodsissuedetailid', 'salesorderdetailid', 'createdBy', 'updatedBy'], 'integer'],
            [['qty', 'price', 'discount', 'totaldiscount', 'vat', 'totalvat', 'totalsales', 'tax', 'totaltax', 'fee', 'totalfee'], 'number'],
            [['createdAt', 'updatedAt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'head_id' => 'Head ID',
            'addressbookid' => 'Addressbookid',
            'productid' => 'Productid',
            'qty' => 'Qty',
            'price' => 'Price',
            'discount' => 'Discount',
            'totaldiscount' => 'Totaldiscount',
            'vat' => 'Vat',
            'totalvat' => 'Totalvat',
            'totalsales' => 'Totalsales',
            'tax' => 'Tax',
            'totaltax' => 'Totaltax',
            'fee' => 'Fee',
            'totalfee' => 'Totalfee',
            'invoiceardetailid' => 'Invoiceardetailid',
            'goodsissuedetailid' => 'Gidetailid',
            'salesorderdetailid' => 'Salesorderdetailid',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'createdBy' => 'Created By',
            'updatedBy' => 'Updated By',
        ];
    }
}
