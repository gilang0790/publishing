<?php

namespace app\modules\common\controllers;

use Yii;
use app\modules\common\models\Supplier;
use app\modules\common\models\search\SupplierSearchModel;
use kartik\form\ActiveForm;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SupplierController implements the CRUD actions for Supplier model.
 */
class SupplierController extends BaseController
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
     * Lists all Supplier models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SupplierSearchModel();
        $searchModel->status = Supplier::STATUS_ACTIVE;
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
     * Creates a new Supplier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Supplier(['scenario' => 'create']);
        $model->isvendor = 1;
        $model->status = Supplier::STATUS_ACTIVE;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', 'Gagal simpan pemasok');
                return $this->redirect(['index']);
            }
                        
            Yii::$app->getSession()->setFlash('success', 'Berhasil simpan pemasok' . ' ' . $model->fullname);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Supplier model.
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
            $model->status = Supplier::STATUS_ACTIVE;
            if($model->save()){
                Yii::$app->getSession()->setFlash('success', 'Berhasil ubah pemasok' . ' ' . $model->fullname);
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Gagal ubah pemasok');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Supplier model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Supplier::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil hapus pemasok' . ' ' . $model->fullname);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal hapus pemasok' . ' ' . $model->fullname);
        }
        return $this->redirect(['index']);
    }
    
    public function actionRestore($id) {
        $model = $this->findModel($id);
        $model->status = Supplier::STATUS_ACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil memulihkan pemasok' . ' ' . $model->fullname);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memulihkan pemasok' . ' ' . $model->fullname);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Supplier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Supplier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Supplier::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
