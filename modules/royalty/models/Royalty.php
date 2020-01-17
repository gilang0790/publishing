<?php

namespace app\modules\royalty\models;

use Yii;

/**
 * This is the model class for table "tr_royalty".
 *
 * @property int $id
 * @property int $plantid
 * @property int $royaltysettingid
 * @property int $addressbookid
 * @property int $productid
 * @property string $totalqty
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
 * @property string $totalpaymentfee
 * @property string $dueperiod
 * @property string $lastfeedate
 * @property string $nextfeedate
 * @property string $lastpaymentdate
 * @property string $nextpaymentdate
 * @property int $invoicearid
 * @property int $goodsissueid
 * @property int $salesorderid
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 */
class Royalty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_royalty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plantid', 'royaltysettingid', 'addressbookid', 'productid', 'dueperiod', 'invoicearid', 'goodsissueid', 'salesorderid', 'createdAt', 'createdBy'], 'required'],
            [['plantid', 'royaltysettingid', 'addressbookid', 'productid', 'invoicearid', 'goodsissueid', 'salesorderid', 'createdBy', 'updatedBy'], 'integer'],
            [['totalqty', 'price', 'discount', 'totaldiscount', 'vat', 'totalvat', 'totalsales', 'tax', 'totaltax', 'fee', 'totalfee', 'totalpaymentfee'], 'number'],
            [['dueperiod', 'lastfeedate', 'nextfeedate', 'lastpaymentdate', 'nextpaymentdate', 'createdAt', 'updatedAt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'plantid' => 'Plantid',
            'royaltysettingid' => 'Royaltysettingid',
            'addressbookid' => 'Addressbookid',
            'productid' => 'Productid',
            'totalqty' => 'Totalqty',
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
            'totalpaymentfee' => 'Totalpaymentfee',
            'dueperiod' => 'Dueperiod',
            'lastfeedate' => 'Lastfeedate',
            'nextfeedate' => 'Nextfeedate',
            'lastpaymentdate' => 'Lastpaymentdate',
            'nextpaymentdate' => 'Nextpaymentdate',
            'invoicearid' => 'Invoicearid',
            'goodsissueid' => 'Goodsissueid',
            'salesorderid' => 'Salesorderid',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
}
