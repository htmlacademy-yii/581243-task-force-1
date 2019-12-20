<?php

namespace frontend\controllers;

use frontend\models\File;
use yii\console\Response;

class FileController extends \yii\web\Controller
{
    /**
     * @param $id
     * @return string|Response|\yii\web\Response
     */
    public function actionDownload($id)
    {
        header("refresh: 5; url=" . \Yii::$app->request->referrer ?? \Yii::$app->homeUrl);
        $file = File::findOne($id);

        if ($file && file_exists(\Yii::$app->basePath . '/..' . $file->path)) {
            return \Yii::$app->response->sendFile(
                \Yii::$app->basePath . '/..' . $file->path,
                $file->title . '.' . $file->type
            );
        }

        return $this->render('error');
    }
}
