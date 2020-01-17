<?php

namespace app\modules\order\controllers;

use Yii;
use app\modules\order\models\Salesorderinfo;
use app\modules\order\models\forms\SalesorderinfoForm;
use app\modules\order\models\Salesorderinfodetail;
use app\modules\order\models\forms\SalesorderinfoGenerate;
use app\modules\order\models\search\SalesorderinfoSearchModel;
use app\modules\order\models\search\SalesorderinfodetailSearchModel;
use app\modules\order\models\search\SalesorderBrowseModel;
use app\models\Model;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * SalesorderinfoController implements the CRUD actions for Goods Issue model.
 */
class SalesorderinfoController extends BaseController {
    /**
     * @inheritdoc
     */
    public function behaviors() {
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
     * Lists all Goods Issue models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SalesorderinfoSearchModel();
        $searchModel->status = Salesorderinfo::STATUS_ACTIVE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionView($id)
    {
        $searchModel = new SalesorderinfodetailSearchModel();
        $searchModel->head_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Salesorderinfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $details = $this->findModelDetail($id);
        $errMsg = "";

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ArrayHelper::merge(
                    ActiveForm::validateMultiple($details),
                    ActiveForm::validate($model)
            );
        }

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($details, 'id', 'id');
            $details = Model::createMultiple(Salesorderinfodetail::classname(),
                    $details);
            Model::loadMultiple($details, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs,
                array_filter(ArrayHelper::map($details, 'id', 'id')));

            foreach ($details as $detail) {
                $detail->head_id = $model->id;
            }

            if ($model->saveModel($errMsg, $details, $deletedIDs)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan info penjualan' . ' #' . $model->salesorder->sotransnum);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'details' => (empty($details)) ? [new Salesorderinfodetail] : $details
        ]);
    }
    
    public function actionGenerate($qq) {
        $model = new SalesorderinfoGenerate();
        $errMsg = "";
        if ($model->generate($errMsg, $qq)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data. Silahkan ubah data info penjualan.');
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }
    
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Salesorderinfo::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil hapus data' . ' ' . $model->salesorder->sotransnum);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal hapus data' . ' ' . $model->salesorder->sotransnum);
        }
        return $this->redirect(['index']);
    }
    
    public function actionRestore($id) {
        $model = $this->findModel($id);
        $model->status = Salesorderinfo::STATUS_ACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil memulihkan data' . ' ' . $model->salesorder->sotransnum);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memulihkan data' . ' ' . $model->salesorder->sotransnum);
        }
        return $this->redirect(['index']);
    }
    
    public function actionBrowse() {
        $searchModel = new SalesorderBrowseModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('browse', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
        /**
     * Finds the Salesorderinfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Salesorderinfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = SalesorderinfoForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

    protected function findModelDetail($id) {
        if (($model = Salesorderinfodetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

}
