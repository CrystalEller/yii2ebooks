<?php

namespace app\controllers\json;


use app\models\Book;
use app\models\Format;
use yii\validators\FileValidator;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class JsonFormController extends Controller
{
    const RESPONSE_OK = 'OK';
    const RESPONSE_NO_DATA = 'No data';


    public function actionSave()
    {
        if (\Yii::$app->request->isPost) {
            if (!empty($_POST['formId']) &&
                !empty($_POST['formFields']) &&
                is_array($_POST['formFields'])
            ) {
                $session = \Yii::$app->session;

                if (empty($session[$_POST['formId']])) {
                    $session[$_POST['formId']] = $_POST['formFields'];
                } else {
                    $session[$_POST['formId']] = array_merge($session[$_POST['formId']], $_POST['formFields']);
                }

                return $this->respond(200, self::RESPONSE_OK);
            }
        }

        return $this->respond(400, self::RESPONSE_NO_DATA);
    }

    protected function respond($httpCode, $status, $data = array())
    {
        $response['status'] = $status;
        $response['data'] = $data;

        \Yii::$app->response->format = Response::FORMAT_JSON;

        \Yii::$app->response->statusCode = $httpCode;

        return $response;
    }
}