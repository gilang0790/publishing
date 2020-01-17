<?php

namespace app\modules\royalty\controllers;

use Yii;
use app\components\AppHelper;
use app\modules\royalty\models\search\InvoiceroyaltyBrowseModel;
use app\modules\royalty\models\Royaltypayment;
use app\modules\royalty\models\forms\RoyaltypaymentApprove;
use app\modules\royalty\models\forms\RoyaltypaymentForm;
use app\modules\royalty\models\forms\RoyaltypaymentGenerate;
use app\modules\royalty\models\forms\RoyaltypaymentReject;
use app\modules\royalty\models\search\RoyaltypaymentSearchModel;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RoyaltypaymentController implements the CRUD actions for Royaltypayment model.
 */
class RoyaltypaymentController extends BaseController
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
     * Lists all Royaltypayment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RoyaltypaymentSearchModel();
        $searchModel->dateFrom = date('01-m-Y');
        $searchModel->dateTo = date('d-m-Y');
        $searchModel->rptransdate = date('d-m-Y');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Royaltypayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RoyaltypaymentForm();
        $errMsg = "";
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveModel($errMsg)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan pembayaran royalti' . $model->rptransnum);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Royaltypayment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $errMsg = "";
        if (!AppHelper::canAccessData('Royalty Payment', $model->rptransnum)) {
            Yii::$app->session->setFlash('error',
                        'Data #' . $model->rptransnum . ' sedang diakses oleh ' . User::getUsername($model->lockBy));
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveModel($errMsg)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan pembayaran royalti' . ' #' . $model->rptransnum);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }
    
    public function actionGenerate($qq) {
        $model = new RoyaltypaymentGenerate();
        $errMsg = "";
        if ($model->generate($errMsg, $qq)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data. Silahkan ubah data pembayaran royalti.');
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }
    
    public function actionApproval($id) {
        $model = $this->findApprovalModel($id);
        $errMsg = "";
        if ($model->approve($errMsg)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data' . ' #' . $model->rptransnum);
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }
    
    public function actionReject($id) {
        $model = $this->findRejectModel($id);
        $errMsg = "";
        if ($model->reject($errMsg)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data' . ' #' . $model->rptransnum);
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }
    
    public function actionBrowse() {
        $searchModel = new InvoiceroyaltyBrowseModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('browse', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Royaltypayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Royaltypayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RoyaltypaymentForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function findApprovalModel($id) {
        if (($model = RoyaltypaymentApprove::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findRejectModel($id) {
        if (($model = RoyaltypaymentReject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
