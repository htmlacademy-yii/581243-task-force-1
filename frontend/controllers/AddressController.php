<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;

class AddressController extends SecuredController
{
    /**
     * @param string $query
     * @return Response
     */
    public function actionIndex(string $query): Response
    {
        return Yii::$app->address->getResponse($query);
    }
}
