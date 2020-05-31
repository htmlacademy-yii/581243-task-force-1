<?php

namespace frontend\components;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Yii;
use yii\caching\TagDependency;
use yii\web\Response;

class AddressComponent
{
    const LANG = 'ru_RU';

    /**
     * @param string $query
     * @return Response
     */
    public function getResponse(string $query): Response
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
            $data = $this->getDataFromYandex($query);

            if ($cache && $query) {
                $this->setDataToCache($query, $data);
            }
        } catch (RequestException $e) {
            $data = [];
        }

        $response->data = $data;
        $response->format = Response::FORMAT_JSON;

        return $response;
    }

    /**
     * @param string $query
     * @return array
     */
    public function getDataFromYandex(string $query): array
    {
        $client = new Client([
            'base_uri' => 'https://geocode-maps.yandex.ru/',
        ]);
        $api_key = Yii::$app->params['apiKey'];
        $city = Yii::$app->user->identity->city;
        $yandexResponse = $client->request('GET', '1.x', [
            'query' => [
                'format' => RequestOptions::JSON,
                'apikey' => $api_key,
                'geocode' => $query,
                'll' => $city ? ($city->long . ',' . $city->lat) : null,
                'lang' => static::LANG,
            ]
        ]);

        $result = json_decode($yandexResponse->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ServerException('Invalid json format');
        }

        if (!isset($result['response']['GeoObjectCollection']['featureMember'])) {
            throw new BadResponseException('Api error');
        }

        if (!empty($result['response']['GeoObjectCollection']['featureMember'])) {
            $data = array_map(function (array $item): array {
                foreach ($item['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['Components'] as $component) {
                    if ($component['kind'] === 'locality') {
                        $locality = $component['name'];
                    }
                }

                return [
                    'city' => $item['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'],
                    'point' => $item['GeoObject']['Point']['pos'],
                    'locality' => $locality ?? null,
                ];
            }, $result['response']['GeoObjectCollection']['featureMember']);
        }

        return $data;
    }

    /**
     * @param string $query
     * @param array $data
     */
    public function setDataToCache(string $query, array $data): void
    {
        $tag = new TagDependency(['tags' => 'yandex_location']);
        Yii::$app->cache->set(sha1($query), json_encode($data),86400, $tag);
    }
}
