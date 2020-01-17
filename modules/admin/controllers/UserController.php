<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\User;
use app\modules\admin\models\search\UserSearchModel;
use kartik\form\ActiveForm;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearchModel();
        $searchModel->status = User::STATUS_ACTIVE;
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User(['scenario' => 'create']);
        $model->status = User::STATUS_ACTIVE;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', 'Gagal membuat user');
                return $this->redirect(['index']);
            }
                        
            Yii::$app->getSession()->setFlash('success', 'Berhasil simpan' . ' ' . $model->username);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
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
            $model->status = User::STATUS_ACTIVE;
            if($model->save()){
                Yii::$app->getSession()->setFlash('success', 'Berhasil ubah user' . ' ' . $model->username);
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Gagal ubah user');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    public function actionChange($id) {
       $model = $this->findModel($id);
       $model->scenario = 'change';
       if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
           Yii::$app->response->format = Response::FORMAT_JSON;
           return ActiveForm::validate($model);
       }
       if ($model->load(Yii::$app->request->post())) {
           $model->status = User::STATUS_ACTIVE;
           if ($model->save()) {
               Yii::$app->getSession()->setFlash('success', 'Berhasil ubah kata sandi user' . ' ' . $model->username);
           } else {
               Yii::$app->getSession()->setFlash('error', 'Gagal ubah kata sandi user');
           }
           return $this->redirect(['index']);
       } else {
           return $this->render('change', [
               'model' => $model,
           ]);
       }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = User::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil hapus user' . ' ' . $model->username);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal hapus user' . ' ' . $model->username);
        }
        return $this->redirect(['index']);
    }
    
    public function actionRestore($id) {
        $model = $this->findModel($id);
        $model->status = User::STATUS_ACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil memulihkan user' . ' ' . $model->username);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memulihkan user' . ' ' . $model->username);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
