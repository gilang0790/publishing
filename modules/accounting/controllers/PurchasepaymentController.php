<?php

namespace app\modules\accounting\controllers;

use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\forms\PurchasepaymentForm;
use app\modules\accounting\models\Invoiceapdetail;
use app\modules\accounting\models\search\InvoiceapBrowseModel;
use app\modules\accounting\models\forms\PurchasepaymentGenerate;
use app\modules\accounting\models\search\PurchasepaymentSearchModel;
use app\modules\accounting\models\forms\PurchasepaymentApprove;
use app\modules\accounting\models\forms\PurchasepaymentReject;
use app\modules\admin\models\User;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class PurchasepaymentController extends BaseController {
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
     * Lists all Purchase Payment models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PurchasepaymentSearchModel();
        $searchModel->dateFrom = date('01-m-Y');
        $searchModel->dateTo = date('d-m-Y');
        $searchModel->pptransdate = date('d-m-Y');
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
    
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $errMsg = "";
        if (!AppHelper::canAccessData('Purchase Payment', $model->pptransnum)) {
            Yii::$app->session->setFlash('error',
                        'Data #' . $model->pptransnum . ' sedang diakses oleh ' . User::getUsername($model->lockBy));
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveModel($errMsg)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan penerimaan pembelian' . ' #' . $model->pptransnum);
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
        $model = new PurchasepaymentGenerate();
        $errMsg = "";
        if ($model->generate($errMsg, $qq)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data. Silahkan ubah data penerimaan pembelian.');
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
                'Berhasil proses data' . ' #' . $model->pptransnum);
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
                'Berhasil proses data' . ' #' . $model->pptransnum);
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }
    
    public function actionBrowse() {
        $searchModel = new InvoiceapBrowseModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('browse', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionPrint($id) {
        $model = $this->findModel($id);
        $details = $this->findModelDetail($id);
        $this->layout = false;
        $contentArray = [];
        
        $contentArray[] = $this->render('print', [
            'model' => $model,
            'details' => $details
        ]);
        
        $content = implode("<pagebreak>", $contentArray);
        
        $pdf = Yii::$app->pdf;
        $pdf->content = $content;
        $pdf->methods = [
            'SetFooter' => ['Pembayaran Pembelian'],
        ];
        return $pdf->render();
    }
        /**
     * Finds the Invoiceap model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoiceap the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = PurchasepaymentForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findApprovalModel($id) {
        if (($model = PurchasepaymentApprove::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findRejectModel($id) {
        if (($model = PurchasepaymentReject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

    protected function findModelDetail($id) {
        if (($model = Invoiceapdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

}
