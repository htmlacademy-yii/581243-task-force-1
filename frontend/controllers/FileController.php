<?php

namespace frontend\controllers;

use frontend\models\File;
use Yii;
use yii\web\Response;

class FileController extends SecuredController
{
    /**
     * @param int $id
     * @return Response
     */
    public function actionDownload(int $id): Response
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
