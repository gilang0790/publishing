<?php

namespace app\modules\common\controllers;

use Yii;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\common\models\forms\UploadProduct;
use kartik\form\ActiveForm;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends BaseController
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
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        
        $searchModel = new ProductSearchModel();
        if (Yii::$app->request->post("exportData", false) == true) {
            $searchModel->exportData();
            Yii::$app->end();
        }
        
        $searchModel->status = Product::STATUS_ACTIVE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $uploadModel = new UploadProduct();
        $errMsg = "";
        $counter = 0;
        
        if ($uploadModel->load(Yii::$app->request->post())) {
            $uploadModel->fileUpload = UploadedFile::getInstance($uploadModel, 'fileUpload');
            
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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product(['scenario' => 'create']);
        $model->status = Product::STATUS_ACTIVE;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', 'Gagal simpan barang');
                return $this->redirect(['index']);
            }
                        
            Yii::$app->getSession()->setFlash('success', 'Berhasil simpan' . ' ' . $model->productcode);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
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
            $model->status = Product::STATUS_ACTIVE;
            if($model->save()){
            Yii::$app->getSession()->setFlash('success', 'Berhasil ubah barang' . ' ' . $model->productcode);
            return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Gagal ubah barang');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Product::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil hapus barang' . ' ' . $model->productcode);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal hapus barang' . ' ' . $model->productcode);
        }
        return $this->redirect(['index']);
    }
    
    public function actionRestore($id) {
        $model = $this->findModel($id);
        $model->status = Product::STATUS_ACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Berhasil memulihkan barang' . ' ' . $model->productcode);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memulihkan barang' . ' ' . $model->productcode);
        }
        return $this->redirect(['index']);
    }
    
    public function actionPrint() {
        $path = Yii::getAlias('@webroot') . '/assets_b/downloads/excel/PRODUCT_TEMPLATE.xlsx';
        Yii::$app->response->sendFile($path);
    }
        /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
