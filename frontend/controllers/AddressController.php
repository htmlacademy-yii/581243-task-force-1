<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use GuzzleHttp\Client;

class AddressController extends SecuredController
{
    /**
     * @return string
     */
    public function actionIndex($query)
    {

        $api_key = Yii::$app->params['apiKey'];

        $client = new Client([
            'base_uri' => 'https://geocode-maps.yandex.ru/',
        ]);

        $response = $client->request('GET', '1.x', [
            'query' => [
                'format' => 'json',
                'apikey' => $api_key,
                'geocode' => $query,
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        $data = array_map(function ($item) {
            return [
                'city' => $item['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'],
                'point' => $item['GeoObject']['Point']['pos'],
            ];
        }, $result['response']['GeoObjectCollection']['featureMember']);
        $response = Yii::$app->response;
        $response->data = $data;
        $response->format = Response::FORMAT_JSON;


        return $response;
    }
}
