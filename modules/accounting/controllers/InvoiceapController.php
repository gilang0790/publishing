<?php

namespace app\modules\accounting\controllers;

use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\forms\InvoiceapForm;
use app\modules\accounting\models\Invoiceapdetail;
use app\modules\accounting\models\forms\InvoiceapApprove;
use app\modules\inventory\models\search\GoodsreceiptBrowseModel;
use app\modules\accounting\models\forms\InvoiceapReject;
use app\modules\accounting\models\forms\InvoiceapGenerate;
use app\modules\accounting\models\search\InvoiceapSearchModel;
use app\modules\accounting\models\search\InvoiceapdetailSearchModel;
use app\modules\admin\models\User;
use yii\helpers\ArrayHelper;
use app\models\Model;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class InvoiceapController extends BaseController {
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
     * Lists all Invoice AP models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new InvoiceapSearchModel();
        $searchModel->dateFrom = date('01-m-Y');
        $searchModel->dateTo = date('d-m-Y');
        $searchModel->aptransdate = date('d-m-Y');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionView($id)
    {
        $searchModel = new InvoiceapdetailSearchModel();
        $searchModel->head_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $details = $this->findModelDetail($id);
        $model->scenario = 'update';
        $errMsg = "";
        if (!AppHelper::canAccessData('Account Payable', $model->aptransnum)) {
            Yii::$app->session->setFlash('error',
                        'Data #' . $model->aptransnum . ' sedang diakses oleh ' . User::getUsername($model->lockBy));
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ArrayHelper::merge(
                    ActiveForm::validateMultiple($details),
                    ActiveForm::validate($model)
            );
        }

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($details, 'id', 'id');
            $details = Model::createMultiple(Invoiceapdetail::classname(),
                    $details);
            Model::loadMultiple($details, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs,
                array_filter(ArrayHelper::map($details, 'id', 'id')));

            foreach ($details as $detail) {
                $detail->head_id = $model->id;
            }

            if ($model->saveModel($errMsg, $details, $deletedIDs)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan faktur pembelian' . ' #' . $model->aptransnum);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'details' => (empty($details)) ? [new Invoiceapdetail] : $details
        ]);
    }
    
    public function actionGenerate($qq) {
        $model = new InvoiceapGenerate();
        $errMsg = "";
        if ($model->generate($errMsg, $qq)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data. Silahkan ubah data faktur pembelian.');
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
                'Berhasil proses data' . ' #' . $model->aptransnum);
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
                'Berhasil proses data' . ' #' . $model->aptransnum);
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }
    
    public function actionBrowse() {
        $searchModel = new GoodsreceiptBrowseModel();
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
            'SetFooter' => ['Faktur Pembelian'],
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
        if (($model = InvoiceapForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findApprovalModel($id) {
        if (($model = InvoiceapApprove::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findRejectModel($id) {
        if (($model = InvoiceapReject::findOne($id)) !== null) {
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
