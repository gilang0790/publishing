<?php

namespace app\modules\inventory\controllers;

use Yii;
use app\components\AppHelper;
use app\modules\common\models\Product;
use app\modules\common\models\Sloc;
use app\modules\inventory\models\Stockopname;
use app\modules\inventory\models\forms\StockopnameApprove;
use app\modules\inventory\models\forms\StockopnameForm;
use app\modules\inventory\models\forms\StockopnameReject;
use app\modules\common\models\search\SlocBrowseModel;
use app\modules\inventory\models\search\StockopnameSearchModel;
use app\modules\inventory\models\search\StockopnamedetailSearchModel;
use app\modules\inventory\models\Stockopnamedetail;
use app\modules\common\models\search\StoragebinSearchModel;
use app\modules\inventory\models\forms\UploadStockopname;
use app\modules\admin\models\User;
use app\models\Model;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use PHPExcel_IOFactory;
use yii\web\UploadedFile;

/**
 * StockopnameController implements the CRUD actions for Stockopname model.
 */
class StockopnameController extends BaseController {
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
     * Lists all Stockopname models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new StockopnameSearchModel();
        $searchModel->dateFrom = date('01-m-Y');
        $searchModel->dateTo = date('d-m-Y');
        $searchModel->bstransdate = date('d-m-Y');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //Upload
        $uploadModel = new UploadStockopname();
        $errMsg = "";
        $counter = 0;
        if ($uploadModel->load(Yii::$app->request->post())) {
            $uploadModel->fileUpload = UploadedFile::getInstance($uploadModel,
                    'fileUpload');

            if ($uploadModel->fileUpload != NULL) {
                $getExtension = end(explode(".", $uploadModel->fileUpload->name));
                $extExtension = strtoupper($getExtension);

                if ($extExtension == "XLS" || $extExtension == "XLSX") {
                    $getFilename1 = date("Y-m-d H:i:s") . '.' . $uploadModel->fileUpload->extension;
                    $getFilename2 = str_replace('-', '', $getFilename1);
                    $getFilename3 = str_replace(':', '', $getFilename2);
                    $filename = str_replace(' ', '', $getFilename3);
                    $inputFileName = Yii::$app->basePath . '/assets_b/uploads/excel/' . $filename;
                    $result = $uploadModel->fileUpload->saveAs($inputFileName);
                    
                    if ($result) {
                        $uploadModel->saveUpload($inputFileName, $counter, $errMsg);
                    }
                } else {
                    $errMsg = "Hanya menerima dokumen excel";
                }
            } else {
                $errMsg = "Silahkan pilih dokumen excel untuk diunggah";
            }
        }
        
        $successMsg = "";
        if ($counter > 0) {
            $successMsg = $counter . " Data berhasil diunggah. ";
        }

        if ($errMsg != "") {
            Yii::$app->session->setFlash('error', $errMsg);
        } else {
            if ($successMsg != "") {
                Yii::$app->session->setFlash('success', $successMsg);
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'uploadModel' => $uploadModel
        ]);
    }
    
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->total = round($model->total);
        $searchModel = new StockopnamedetailSearchModel();
        $searchModel->head_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Stockopname model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($qq) {
        $model = new StockopnameForm();
        $model->slocid = $qq;
        $details = [new Stockopnamedetail];
        $errMsg = "";
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ArrayHelper::merge(
                    ActiveForm::validateMultiple($details),
                    ActiveForm::validate($model)
            );
        }

        if ($model->load(Yii::$app->request->post())) {
            $details = Model::createMultiple(Stockopnamedetail::classname());
            Model::loadMultiple($details, Yii::$app->request->post());

            foreach ($details as $detail) {
                $detail->head_id = 0;
            }

            if ($model->saveModel($errMsg, $details)) {
                Yii::$app->session->setFlash('success',
                    'Berhasil simpan stock opname' . ' #' . $model->bstransnum);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
            return $this->redirect(['index']);
        } else {
            $model->id = 0;
            return $this->render('create',
                    [
                    'model' => $model,
                    'details' => $details,
            ]);
        }
    }

    /**
     * Updates an existing Stockopname model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $details = $this->findModelDetail($id);
        $errMsg = "";
        if (!AppHelper::canAccessData('Stock Opname', $model->bstransnum)) {
            Yii::$app->session->setFlash('error',
                        'Data #' . $model->bstransnum . ' sedang diakses oleh ' . User::getUsername($model->lockBy));
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
            $details = Model::createMultiple(Stockopnamedetail::classname(),
                    $details);
            Model::loadMultiple($details, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs,
                array_filter(ArrayHelper::map($details, 'id', 'id')));

            foreach ($details as $detail) {
                $detail->head_id = $model->id;
            }

            $valid1 = $model->validate();
            $valid2 = Model::validateMultiple($details);
            $valid = $valid1 && $valid2;

            // jika valid, mulai proses penyimpanan
            if ($valid) {
                if ($model->saveModel($errMsg, $details, $deletedIDs)) {
                    Yii::$app->session->setFlash('success',
                        'Berhasil simpan stock opname' . ' #' . $model->bstransnum);
                } else {
                    Yii::$app->session->setFlash('error', $errMsg);
                }
                return $this->redirect(['index']);
            } else {
                return $this->render('update',[
                    'model' => $model,
                    'details' => $details,
                    'error' => 'valid1: ' . print_r($valid1, true) . ' - valid2: ' . print_r($valid2,
                        true),
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'details' => (empty($details)) ? [new Stockopnamedetail] : $details
        ]);
    }
    
    public function actionApproval($id) {
        $model = $this->findApprovalModel($id);
        $errMsg = "";
        if ($model->approve($errMsg)) {
            Yii::$app->session->setFlash('success',
                'Berhasil proses data' . ' #' . $model->bstransnum);
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
                'Berhasil proses data' . ' #' . $model->bstransnum);
        } else {
            Yii::$app->session->setFlash('error',
                $errMsg);
        }
        return $this->redirect(['index']);
    }

    public function actionDownload() {
        $path = Yii::getAlias('@webroot') . '/assets_b/downloads/excel/STOCK_OPNAME_TEMPLATE.xlsx';
        Yii::$app->response->sendFile($path);
    }

    public function actionUpload() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        if (Yii::$app->request->post() !== null) {
            $result[0]['uploadData'] = [];
            if (isset($_FILES)) {
                if (0 < $_FILES['file']['error']) {
                    $result[0]['errMsg'] = 'Gagal saat unggah data ' . $_FILES['file']['error'];
                } else {
                    $inputFileName = Yii::$app->basePath . '/assets_b/uploads/excel/' . $_FILES['file']['name'];
                    if (file_exists($inputFileName)) {
                        unlink($inputFileName);
                    }
                    move_uploaded_file($_FILES['file']['tmp_name'],
                        $inputFileName);
                    $errMsg = '';
                    $uploadData = [];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);

                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();

                    $result = [];
                    $i = 0;
                    for ($row = 2; $row <= $highestRow; ++$row) {
                        $errVal = '';
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                            NULL, TRUE, FALSE);
                        if (!empty($rowData[0][1])) {
                            $productName = (!is_null($rowData[0][1]) ? $rowData[0][1] : '');
                            $productModel = Product::find()->where(['like', 'productname', $productName])->one();
                            $productID = $productModel ? $productModel->productid : 0;
                            if ($productID == 0) {
                                $errVal .= 'Barang tidak terdaftar';
                            }
                            $qty = (!is_null($rowData[0][2]) ? $rowData[0][2] : 0);
                            $storagebinDesc = (!is_null($rowData[0][3]) ? $rowData[0][3] : '');
                            $storagebinModel = StoragebinSearchModel::findActive()->where(['like', 'description', $storagebinDesc])->one();
                            $storagebinID = $storagebinModel ? $storagebinModel->storagebinid : 0;
                            if ($storagebinID == 0) {
                                $errVal .= 'Rak tidak terdaftar';
                            }
                            $hpp = (!is_null($rowData[0][4]) ? $rowData[0][4] : 0);
                            $type = (!is_null($rowData[0][5]) ? $rowData[0][5] : 0);
                            $itemTotal = (!is_null($rowData[0][6]) ? $rowData[0][6] : 0);

                            $uploadData[$i]['productID'] = $productID;
                            $uploadData[$i]['qty'] = $qty;
                            $uploadData[$i]['storagebinID'] = $storagebinID;
                            $uploadData[$i]['hpp'] = $hpp;
                            $uploadData[$i]['type'] = $type;
                            $uploadData[$i]['itemTotal'] = $itemTotal;

                            if ($errVal != '') {
                                $errMsg .= '- Baris Excel ' . $row . " : " . rtrim($errVal,
                                        ', ') . "<br>";
                            }

                            $i += 1;
                        } else {
                            break;
                        }
                    }
                    $result[0]['uploadData'] = $uploadData;
                    $result[0]['errMsg'] = $errMsg;

                    unlink($inputFileName);
                }
            } else {
                $result[0]['errMsg'] = 'Silahkan pilih dokumen excel.';
            }
            return $result;
        }
    }
    
    public function actionBrowse() {
        $searchModel = new SlocBrowseModel();
        $searchModel->status = Sloc::STATUS_ACTIVE;
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
            'SetFooter' => ['Penyesuaian Stok'],
        ];
        return $pdf->render();
    }
        /**
     * Finds the Stockopname model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stockopname the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = StockopnameForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findApprovalModel($id) {
        if (($model = StockopnameApprove::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findRejectModel($id) {
        if (($model = StockopnameReject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

    protected function findModelDetail($id) {
        if (($model = Stockopnamedetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

}
