<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Menuaccess;
use app\modules\admin\models\search\MenuaccessSearchModel;
use kartik\form\ActiveForm;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * MenuaccessController implements the CRUD actions for Menuaccess model.
 */
class MenuaccessController extends BaseController
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
     * Lists all Menuaccess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuaccessSearchModel();
        $searchModel->status = Menuaccess::STATUS_ACTIVE;
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
     * Creates a new Menuaccess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menuaccess(['scenario' => 'create']);
        $model->status = Menuaccess::STATUS_ACTIVE;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', 'Failed to create menu');
                return $this->redirect(['index']);
            }
                        
            Yii::$app->getSession()->setFlash('success', 'Successfully save');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Menuaccess model.
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
            $model->status = Menuaccess::STATUS_ACTIVE;
            if($model->save()){
            Yii::$app->getSession()->setFlash('success', 'Successfully update menu' . ' ' . $model->menuname);
            return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Failed to update menu');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Menuaccess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Menuaccess::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Successfully deactivate menu' . ' ' . $model->menuname);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to deactivate menu' . ' ' . $model->menuname);
        }
        return $this->redirect(['index']);
    }
    
    public function actionRestore($id) {
        $model = $this->findModel($id);
        $model->status = Menuaccess::STATUS_ACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Successfully restore menu' . ' ' . $model->menuname);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to restore menu' . ' ' . $model->menuname);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Menuaccess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menuaccess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menuaccess::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
