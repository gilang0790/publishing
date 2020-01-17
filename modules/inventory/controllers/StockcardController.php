<?php

namespace app\modules\inventory\controllers;

use Yii;
use app\modules\inventory\models\Stockcard;
use app\modules\inventory\models\search\StockSearchModel;
use app\modules\inventory\models\search\StockCardSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StockcardController implements the CRUD actions for Stockcard model.
 */
class StockcardController extends Controller
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
     * Lists all Stockcard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockSearchModel();

        if (Yii::$app->request->queryParams) {
            $searchModel->load(Yii::$app->request->queryParams);
            
            $downloadReport = Yii::$app->request->get("downloadReport", null);
            if ($downloadReport !== null) {
                $searchModel->searchStock();
                return null;
            }
        }
        
        return $this->render('index', [
            'model' => $searchModel
        ]);
    }

    public function actionView($search)
    {
        //Pjax search
        $decoded = json_decode($search);
        $searchTransdate = '';
        
        $currentDate = date('Y-m-d');
        $d1 = explode('-', $currentDate);
        $searchDateFrom = '01-' . $d1['1'] . '-' . $d1['0'];
        $searchTransdate = $d1['2'] . '-' . $d1['1'] . '-' . $d1['0'];
        
        $model = new StockCardSearchModel();
        $model->dateFrom = $searchDateFrom;
        $model->dateTo = $searchTransdate;
        $model->transdate = $searchDateFrom . ' - ' . $searchTransdate;
        $model->plantid = isset($decoded->plantid) ? $decoded->plantid : '';
        $model->slocid = isset($decoded->slocid) ? $decoded->slocid : '';
        $model->categoryid = isset($decoded->categoryid) ? $decoded->categoryid : '';
        $model->productname = isset($decoded->productname) ? $decoded->productname : '';
        $model->productcode = isset($decoded->productcode) ? $decoded->productcode : '';

        $model->load(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Stockcard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Stockcard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Stockcard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stockcard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stockcard::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
