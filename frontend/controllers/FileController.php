<?php

namespace frontend\controllers;

use frontend\models\File;
use yii\console\Response;
use Yii;

class FileController extends \yii\web\Controller
{
    /**
     * @param $id
     * @return string|Response|\yii\web\Response
     */
    public function actionDownload($id)
    {
        $file = File::findOne($id);

        if ($file && file_exists(Yii::$app->basePath . '/..' . $file->path)) {
            return Yii::$app->response->sendFile(
                Yii::$app->basePath . '/..' . $file->path,
                $file->title . '.' . $file->type
            );
        } else {
            return $this->redirect(Yii::$app->request->referrer, 404);
        }
    }
}
