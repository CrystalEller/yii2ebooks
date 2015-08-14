<?php

namespace app\controllers;

use app\models\Book;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    public $layout = 'column1';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Lists all Book models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Book::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Book();

        if (!empty(Yii::$app->request->post())) {
            $model->setAttributes(Yii::$app->request->post());
            $model->authors = Yii::$app->request->post('authors');
            $model->tags = Yii::$app->request->post('tags');
            $model->formats = Yii::$app->request->post('formats');

            if ($model->save()) {
                $bookFiles = \Yii::$app->basePath . '/files/tmp/book-form/1/';
                $imgFile = \Yii::$app->basePath . '/web/image/book/tmp/1/';

                $this->copyFiles($bookFiles, \Yii::$app->basePath . '/files/books/' . $model->id . '/');
                $this->copyFiles($imgFile, \Yii::$app->basePath . '/web/image/book/' . $model->id . '/');

                $this->deleteFiles(\Yii::$app->basePath . '/files/tmp/book-form/1/');
                $this->deleteFiles(\Yii::$app->basePath . '/web/image/book/tmp/1/');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!empty(Yii::$app->request->post())) {
            $model->setAttributes(Yii::$app->request->post());
            $model->authors = Yii::$app->request->post('authors');
            $model->tags = Yii::$app->request->post('tags');
            $model->formats = Yii::$app->request->post('formats');

            if ($model->save()) {
                $bookFiles = \Yii::$app->basePath . '/files/tmp/book-form/1/';
                $imgFile = \Yii::$app->basePath . '/web/image/book/tmp/1/';

                $this->copyFiles($bookFiles, \Yii::$app->basePath . '/files/books/' . $model->id . '/');
                $this->copyFiles($imgFile, \Yii::$app->basePath . '/web/image/book/' . $model->id . '/');

                $this->deleteFiles(\Yii::$app->basePath . '/files/tmp/book-form/1/');
                $this->deleteFiles(\Yii::$app->basePath . '/web/image/book/tmp/1/');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionFile()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (\Yii::$app->request->isPost) {
            $book = new Book();

            $book->imageFile = UploadedFile::getInstanceByName('imageFile');
            $book->bookFile = UploadedFile::getInstanceByName('bookFile');

            if (!$book->validate(['imageFile', 'bookFile'])) {
                \Yii::$app->response->statusCode = 400;

                return ['status' => 'Err', 'errors' => $book->getErrors()];
            } else {

                if (!empty($book->imageFile)) {
                    $fileDir = \Yii::$app->basePath . '/web/image/book/tmp/1/';
                    if (!file_exists($fileDir)) {
                        mkdir($fileDir, 0755, true);
                    }

                    array_map('unlink', glob(\Yii::$app->basePath . $fileDir . '*'));
                    $book->imageFile
                        ->saveAs($fileDir . $book->imageFile->baseName . '.' . $book->imageFile->extension);
                }

                if (!empty($book->bookFile)) {
                    $fileDir = \Yii::$app->basePath . '/files/tmp/book-form/1/book-file/';
                    if (!file_exists($fileDir)) {
                        mkdir($fileDir, 0755, true);
                    }

                    $book->bookFile
                        ->saveAs($fileDir . $book->bookFile->baseName . '.' . $book->bookFile->extension);
                }

                \Yii::$app->response->statusCode = 200;

                return ['status' => 'OK'];
            }
        }

        if (\Yii::$app->request->isGet) {
            if (!empty(\Yii::$app->request->get('remove')) &&
                !empty(\Yii::$app->request->get('file'))
            ) {
                unlink(\Yii::$app->basePath . '/files/tmp/book-form/1/book-file/' . \Yii::$app->request->get('file'));

                \Yii::$app->response->statusCode = 200;

                return ['status' => 'OK'];
            }
        }
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function deleteFiles($target)
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK);

            foreach ($files as $file) {
                $this->deleteFiles($file);
            }

            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }

    protected function copyFiles($src, $dst)
    {
        if (file_exists($dst))
            rrmdir($dst);
        if (is_dir($src)) {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file)
                if ($file != "." && $file != "..")
                    $this->copyFiles("$src/$file", "$dst/$file");
        } else if (file_exists($src))
            copy($src, $dst);
    }
}
