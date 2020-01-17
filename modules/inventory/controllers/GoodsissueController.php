<?php

namespace app\modules\inventory\controllers;

use Yii;
use app\components\AppHelper;
use app\modules\inventory\models\forms\GoodsissueForm;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\admin\models\Groupmenuauth;
use app\modules\inventory\models\forms\GoodsissueApprove;
use app\modules\inventory\models\forms\GoodsissueGenerate;
use app\modules\inventory\models\forms\GoodsissueReject;
use app\modules\inventory\models\search\GoodsissueSearchModel;
use app\modules\inventory\models\search\GoodsissuedetailSearchModel;
use app\modules\order\models\search\SalesorderBrowseModel;
use app\modules\inventory\models\Stock;
use app\modules\common\models\Storagebin;
use app\modules\admin\models\User;
use app\models\Model;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * GoodsissueController implements the CRUD actions for Goods Issue model.
 */
class GoodsissueController extends BaseController {
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
        $searchModel = new GoodsissueSearchModel();
        $searchModel->dateFrom = date('01-m-Y');
        $searchModel->dateTo = date('d-m-Y');
        $searchModel->gitransdate = date('d-m-Y');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionView($id)
    {
        $searchModel = new GoodsissuedetailSearchModel();
        $searchModel->head_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Goodsissue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $details = $this->findModelDetail($id);
        $errMsg = "";
        if (!AppHelper::canAccessData('Goods Issue', $model->gitransnum)) {
            Yii::$app->session->setFlash('error',
                        'Data #' . $model->gitransnum . ' sedang diakses oleh ' . User::getUsername($model->lockBy));
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
            $details = Model::createMultiple(Goodsissuedetail::classname(),
                    $details);
            Model::loadMultiple($details, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs,
                array_filter(ArrayHelper::map($details, 'id', 'id')));

            foreach ($details as $detail) {
                $detail->head_id = $model->id;
            }

            if ($model->saveModel($errMsg, $details, $deletedIDs)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan pengeluaran barang' . ' #' . $model->gitransnum);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'details' => (empty($details)) ? [new Goodsissuedetail] : $details
        ]);
    }
    
    public function actionLists()
    {
        $request = Yii::$app->request;
        $slocID = $request->post('slocid');
        $productID = $request->post('productid');
        $qty = $request->post('qty');
        
        $listCompany = '(-1)';
        $listPlant = '(-1)';
        $listSloc = '(-1)';
        $query = Storagebin::find();
        $query->innerJoinWith('sloc.plant.company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Groupmenuauth::getObject('sloc')) {
            $listSloc = Groupmenuauth::getObject('sloc');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("ms_sloc.slocid IN $listSloc");
        $query->andWhere(['ms_storagebin.slocid' => $slocID]);
        $query->andWhere(Storagebin::tableName() . '.status = true');
        $queryAll = $query->all();
        
        if ($queryAll) {
            foreach($queryAll as $data){
                $stock = Stock::getStock($slocID, $data->storagebinid, $productID);
                if ($stock >= $qty) {
                    echo "<option value='".$data->storagebinid."'>".$data->description."</option>";
                } else {
                    echo "<option></option>";
                }
            }
        }
    }
    
    public function actionGenerate($qq) {
        $model = new GoodsissueGenerate();
        $errMsg = "";
        if ($model->generate($errMsg, $qq)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data. Silahkan ubah data pengeluaran barang.');
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
                'Berhasil proses data' . ' #' . $model->gitransnum);
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
                'Berhasil proses data' . ' #' . $model->gitransnum);
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
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
            'SetFooter' => ['Pengeluaran Barang'],
        ];
        return $pdf->render();
    }
        /**
     * Finds the Goodsissue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Goodsissue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = GoodsissueForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findApprovalModel($id) {
        if (($model = GoodsissueApprove::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findRejectModel($id) {
        if (($model = GoodsissueReject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

    protected function findModelDetail($id) {
        if (($model = Goodsissuedetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

}
