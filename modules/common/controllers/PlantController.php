<?php

namespace app\modules\common\controllers;

use Yii;
use app\modules\common\models\Plant;
use app\modules\common\models\search\PlantSearchModel;
use kartik\form\ActiveForm;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PlantController implements the CRUD actions for Plant model.
 */
class PlantController extends BaseController
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
     * Lists all Plant models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlantSearchModel();
        $searchModel->status = Plant::STATUS_ACTIVE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Plant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Plant(['scenario' => 'create']);
        $model->status = Plant::STATUS_ACTIVE;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', 'Gagal simpan kantor cabang');
                return $this->redirect(['index']);
            }
                        
            Yii::$app->getSession()->setFlash('success', 'Berhasil simpan' . ' ' . $model->plantcode);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Plant model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->status = Plant::STATUS_ACTIVE;
            if($model->save()){
            Yii::$app->getSession()->setFlash('success', 'Berhasil ubah kantor cabang' . ' ' . $model->plantcode);
            return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Gagal ubah kantor cabang');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Plant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Plant::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil hapus kantor cabang' . ' ' . $model->plantcode);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal hapus kantor cabang' . ' ' . $model->plantcode);
        }
        return $this->redirect(['index']);
    }
    
    public function actionRestore($id) {
        $model = $this->findModel($id);
        $model->status = Plant::STATUS_ACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil memulihkan kantor cabang' . ' ' . $model->plantcode);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memulihkan kantor cabang' . ' ' . $model->plantcode);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Plant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Plant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Plant::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
