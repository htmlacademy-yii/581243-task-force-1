<?php

namespace frontend\controllers;

use GuzzleHttp\Exception\RequestException;
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
        $response = Yii::$app->response;
        $cache = Yii::$app->cache;

        if ($cache && $query) {
            if ($cacheData = Yii::$app->cache->get(sha1($query))) {
                $response->data = json_decode($cacheData, true);
                $response->format = Response::FORMAT_JSON;

                return $response;
            }
        }

        try {
            $data = Yii::$app->address->getDataFromYandex($query);

            if ($cache && $query) {
                Yii::$app->address->setDataToCache($query, $data);
            }
        } catch (RequestException $e) {
            $data = [];
        }

        $response->data = $data;
        $response->format = Response::FORMAT_JSON;

        return $response;
    }
}
