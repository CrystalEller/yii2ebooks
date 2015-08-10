<?php

namespace app\controllers;


use yii\base\Exception;
use yii\web\Controller;
use yii\web\Response;

class JsonCRUDController extends Controller
{
    const RESPONSE_OK = 'OK';
    const RESPONSE_NO_DATA = 'No data';
    const RESPONSE_NOT_FOUND = 'Not found';
    const RESPONSE_VALIDATION_ERRORS = 'Validation errors';

    public $modelName;
    private $model;
    private $modelNamespace = 'app\\models\\';


    public function init()
    {
        parent::init();
        if (empty($this->modelName)) {
            throw new Exception("You should set modelName before
        using JsonApiController.");
        } else {
            $name = $this->modelNamespace . $this->modelName;
            $this->model = new $name;
            \Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    public function actionCreate()
    {
        if (empty(\Yii::$app->request->post())) {
            return $this->respond(400, self::RESPONSE_NO_DATA);
        }

        $this->model->setAttributes(\Yii::$app->request->post());

        if ($this->model->save()) {
            return $this->respond(200, self::RESPONSE_OK, array('id' => $this->model->id));
        } else {
            return $this->respond(400, self::RESPONSE_VALIDATION_ERRORS, $this->model->getErrors());
        }
    }

    public function actionGet($pk)
    {
        $model = $this->model;
        $model = $model::findOne($pk);

        if (!$model) {
            return $this->respond(404, self::RESPONSE_NOT_FOUND);
        }

        return $this->respond(200, self::RESPONSE_OK, $model->getAttributes());
    }

    public function actionUpdate($pk)
    {
        if (empty(\Yii::$app->request->post())) {
            $model = $this->model;
            $model = $model::findOne($pk);
        }

        if (!$model) {
            return $this->respond(404, self::RESPONSE_NOT_FOUND);
        }

        $model->setAttributes(\Yii::$app->request->post());

        if ($model->save()) {
            return $this->respond(200, self::RESPONSE_OK);
        } else {
            return $this->respond(400, self::RESPONSE_VALIDATION_ERRORS, $model->getErrors());
        }
    }


    public function actionDelete($pk)
    {
        $model = $this->model;
        $model = $model::findOne($pk);

        if ($model->delete()) {
            return $this->respond(200, self::RESPONSE_OK);
        } else {
            return $this->respond(404, self::RESPONSE_NOT_FOUND);
        }
    }

    protected function respond($httpCode, $status, $data = array())
    {
        $response['status'] = $status;
        $response['data'] = $data;

        \Yii::$app->response->statusCode = $httpCode;

        return $response;
    }
}