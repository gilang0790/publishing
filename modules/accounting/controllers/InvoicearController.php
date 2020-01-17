<?php

namespace app\modules\accounting\controllers;

use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\forms\InvoicearForm;
use app\modules\accounting\models\Invoiceardetail;
use app\modules\accounting\models\forms\InvoicearApprove;
use app\modules\inventory\models\search\GoodsissueBrowseModel;
use app\modules\accounting\models\forms\InvoicearReject;
use app\modules\accounting\models\forms\InvoicearGenerate;
use app\modules\accounting\models\search\InvoicearSearchModel;
use app\modules\accounting\models\search\InvoiceardetailSearchModel;
use app\modules\admin\models\User;
use yii\helpers\ArrayHelper;
use app\models\Model;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InvoicearController implements the CRUD actions for Goods Issue model.
 */
class InvoicearController extends BaseController {
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
     * Lists all Invoice AR models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new InvoicearSearchModel();
        $searchModel->dateFrom = date('01-m-Y');
        $searchModel->dateTo = date('d-m-Y');
        $searchModel->artransdate = date('d-m-Y');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionView($id)
    {
        $searchModel = new InvoiceardetailSearchModel();
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
        if (!AppHelper::canAccessData('Account Receivable', $model->artransnum)) {
            Yii::$app->session->setFlash('error',
                        'Data #' . $model->artransnum . ' sedang diakses oleh ' . User::getUsername($model->lockBy));
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
            $details = Model::createMultiple(Invoiceardetail::classname(),
                    $details);
            Model::loadMultiple($details, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs,
                array_filter(ArrayHelper::map($details, 'id', 'id')));

            foreach ($details as $detail) {
                $detail->head_id = $model->id;
            }

            if ($model->saveModel($errMsg, $details, $deletedIDs)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan faktur penjualan' . ' #' . $model->artransnum);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'details' => (empty($details)) ? [new Invoiceardetail] : $details
        ]);
    }
    
    public function actionGenerate($qq) {
        $model = new InvoicearGenerate();
        $errMsg = "";
        if ($model->generate($errMsg, $qq)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data. Silahkan ubah data faktur penjualan.');
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
                'Berhasil proses data' . ' #' . $model->artransnum);
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
                'Berhasil proses data' . ' #' . $model->artransnum);
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }
    
    public function actionBrowse() {
        $searchModel = new GoodsissueBrowseModel();
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
            'SetFooter' => ['Faktur Penjualan'],
        ];
        return $pdf->render();
    }
        /**
     * Finds the Invoicear model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoicear the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = InvoicearForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findApprovalModel($id) {
        if (($model = InvoicearApprove::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findRejectModel($id) {
        if (($model = InvoicearReject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

    protected function findModelDetail($id) {
        if (($model = Invoiceardetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

}
