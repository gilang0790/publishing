<?php

namespace app\modules\royalty\controllers;

use Yii;
use app\modules\royalty\models\Invoiceroyalty;
use app\modules\royalty\models\search\InvoiceroyaltySearchModel;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InvoiceroyaltyController implements the CRUD actions for Invoiceroyalty model.
 */
class InvoiceroyaltyController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Invoiceroyalty models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceroyaltySearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
