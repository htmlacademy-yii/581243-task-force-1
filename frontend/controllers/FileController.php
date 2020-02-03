<?php

namespace frontend\controllers;

use frontend\models\File;
use yii\console\Response;
use Yii;

class FileController extends SecuredController
{
    /**
     * @param $id
     * @return string|Response|\yii\web\Response
     */
    public function actionDownload($id)
    {
        $file = File::findOne($id);

        if ($file && file_exists($file->path)) {
            return Yii::$app->response->sendFile(
                $file->path,
                $file->title . '.' . $file->type
            );
        } else {
            return $this->redirect(Yii::$app->request->referrer, 404);
        }
    }
}
