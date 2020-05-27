<?php

namespace frontend\controllers;

use frontend\models\AccountForm;
use frontend\models\Category;
use frontend\models\City;
use frontend\models\LoginForm;
use frontend\models\User;
use frontend\models\UserFilter;
use frontend\models\UserSettings;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class UserController extends SecuredController
{
    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $userFilter = new UserFilter();

        if (Yii::$app->request->getIsPost()) {
            $userFilter->load(Yii::$app->request->post());
        }

        if (!is_array($userFilter->categories)) {
            $userFilter->categories = [];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Yii::$app->userData->getList($userFilter),
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('index', [
            'userFilter' => $userFilter,
            'categoriesProvider' => new ActiveDataProvider([
                'query' => Category::find(),
            ]),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShow(int $id): string
    {
        $user = User::findOne($id);
        $currentUser = Yii::$app->user->identity;

        if (($user->user_status === User::ROLE_CLIENT) &&
            ($user->id !== $currentUser->id)) {
            throw new NotFoundHttpException();
        }

        return $this->render('view', [
            'user' => $user,
            'hideContacts' => Yii::$app->userData->hideContacts($user),
            'favorite' => $currentUser->getFavoriteUsers()->where(['id' => $id])->one() ? true : false,
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionFavorite(int $id): Response
    {
        $user = Yii::$app->user->identity;
        $favouriteUser = User::findOne($id);

        if ($user->getFavoriteUsers()->where(['id' => $id])->one()) {
            $user->unlink('favoriteUsers', $favouriteUser);
        } else {
            $user->link('favoriteUsers', $favouriteUser);
        }

        return $this->redirect(Url::to(['/users/view/' . $id]));
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ]
        ];
    }

    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionSignup()
    {
        $user = new User();

        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());

            if ($user->validate()) {
                $user->password = Yii::$app->getSecurity()
                    ->generatePasswordHash($user->password);
                $user->save();
                UserSettings::firstOrCreate($user);

                return $this->redirect(Url::to(['/task/']));
            } else {
                $errors = $user->getErrors();
            }
        }

        return $this->render('signup', [
            'user' => $user,
            'cities'  => new ArrayDataProvider([
                'allModels' => City::find()->select('city')->indexBy('id')->column(),
            ]),
            'errors' => $errors ?? [],
        ]);
    }

    /**
     * @return array|Response
     */
    public function actionLogin()
    {
        if (Yii::$app->request->getIsPost()) {
            $loginForm = new LoginForm();
            $loginForm->load(Yii::$app->request->post());
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($loginForm);
            }
            if ($loginForm->validate()) {
                $user = $loginForm->getUser();
                Yii::$app->user->login($user);
                return $this->redirect(Url::to(['/task/']));
            }
        }
    }

    /**
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->redirect(Url::to(['/']));
    }

    /**
     * @throws \Exception
     */
    public function actionImages(): void
    {
        $user = Yii::$app->user->identity;
        $accountForm = new AccountForm();

        if (Yii::$app->request->getIsPost()) {
            $accountForm->load(Yii::$app->request->post());

            if (!empty($images = $accountForm->uploadImages())) {
                Yii::$app->userData->syncImages($user, $images);
            }
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionAccount(): string
    {
        $user = Yii::$app->user->identity;

        $accountForm = new AccountForm();
        $userSettings = UserSettings::firstOrCreate($user);

        $accountForm->attributes = $user->attributes;

        if (Yii::$app->request->getIsPost()) {
            $accountForm->load(Yii::$app->request->post());

            if ($accountForm->validate()) {
                $user->attributes = $accountForm->attributes;
                $user->save();

                $userSettings->attributes = $accountForm->attributes;
                $userSettings->save();

                Yii::$app->userData->syncCategories($user, (is_array($accountForm->categories) ? $accountForm->categories : []));

                $avatar = $accountForm->uploadAvatar();
                if ($avatar) {
                    $user->link('avatar', $avatar);
                    $accountForm->avatar = null;
                }
            }
        } else {
            $accountForm->attributes = $userSettings->attributes;
        }

        return $this->render('account', [
            'accountForm' => $accountForm,
            'categoriesProvider' => new ActiveDataProvider([
                'query' => Category::find(),
            ]),
            'cities' => new ArrayDataProvider([
                'allModels' => City::find()->select('city')->indexBy('id')->column(),
            ]),
            'user' => $user,
        ]);
    }

    /**
     * @param $client
     * @return Response
     * @throws Exception]
     */
    public function onAuthSuccess($client): Response
    {
        $attributes = $client->getUserAttributes();
        $user = User::find()->where(['vk_id' => $attributes['id']])->one();

        if (!$user) {
            $user = new User();
            $user->vk_id = $attributes['id'];
            $user->email = $attributes['email'];
            $user->name = $attributes['first_name'];
            $user->last_name = $attributes['last_name'] ?? null;
            $user->password = Yii::$app->getSecurity()
                ->generatePasswordHash(Yii::$app->security->generateRandomString(8));
            if (isset($attributes['city']['title'])) {
                $user->city_id = City::find()->where(['city' => $attributes['city']['title']])->one()->id ?? null;
            }

            if (!($user->validate() && $user->save())) {
                return $this->redirect(Url::to(['/']));
            }
        }

        Yii::$app->user->login($user);
        return $this->redirect(Url::to(['/task/']));
    }
}
