<?php

namespace frontend\controllers;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Yii;
use yii\caching\TagDependency;
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

        $response = Yii::$app->response;
        $cache = Yii::$app->cache;

        if ($cache && $query) {
            $key = sha1($query);

            if ($cacheData = Yii::$app->cache->get($key)) {
                $response->data = json_decode($cacheData, true);
                $response->format = Response::FORMAT_JSON;

                return $response;
            }
        }

        $client = new Client([
            'base_uri' => 'https://geocode-maps.yandex.ru/',
        ]);

        try {
            $yandexResponse = $client->request('GET', '1.x', [
                'query' => [
                    'format' => RequestOptions::JSON,
                    'apikey' => $api_key,
                    'geocode' => $query,
                ]
            ]);

            $result = json_decode($yandexResponse->getBody()->getContents(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ServerException('Invalid json format');
            }

            if (!isset($result['response']['GeoObjectCollection']['featureMember'])) {
                throw new BadResponseException('Api error');
            }

            $data = array_map(function ($item) {
                return [
                    'city' => $item['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'],
                    'point' => $item['GeoObject']['Point']['pos'],
                ];
            }, $result['response']['GeoObjectCollection']['featureMember']);

            if ($cache && $query) {
                $key = sha1($query);

                $tag = new TagDependency(['tags' => 'yandex_location']);
                Yii::$app->cache->set($key, json_encode($data),86400, $tag);
            }
        } catch (RequestException $e) {
            $data = [];
        }

        $response->data = $data;
        $response->format = Response::FORMAT_JSON;

        return $response;
    }
}
