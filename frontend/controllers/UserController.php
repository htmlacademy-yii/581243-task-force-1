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
use yii\helpers\Url;
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
        $categories = Category::find()->all();

        $usersBuilder = User::find()->where(['user_status' => User::ROLE_EXECUTOR])
            ->orderBy(['users.created_at' => SORT_ASC]);

        if ($sort = \Yii::$app->request->get('sort_by')) {
            $usersBuilder = User::sortBy($usersBuilder, $sort);
        }

        if (\Yii::$app->request->getIsPost()) {
            $userFilter->load(\Yii::$app->request->post());
            $usersBuilder = User::filter($usersBuilder, $userFilter);
        }

        if (!is_array($userFilter->categories)) {
            $userFilter->categories = [];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $usersBuilder,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->render('index', [
            'userFilter' => $userFilter,
            'categories' => $categories,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     */
    public function actionShow(int $id): string
    {
        $user = User::findOne($id);
        $currentUser = Yii::$app->user->identity;

        return $this->render('view', [
            'user' => $user,
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
        $cities = City::find()->select('city')->indexBy('id')->column();

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
            'cities' => $cities,
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
     * @return string
     * @throws \Exception
     */
    public function actionAccount(): string
    {
        $user = Yii::$app->user->identity;
        $categories = Category::find()->all();
        $cities = City::find()->select('city')->indexBy('id')->column();

        $accountForm = new AccountForm();
        $userSettings = UserSettings::firstOrCreate($user);

        $accountForm->attributes = $user->attributes;
        $accountForm->attributes = $userSettings->attributes;

        if (Yii::$app->request->getIsPost()) {
            $accountForm->load(Yii::$app->request->post());

            if (!empty($images = $accountForm->uploadImages())) {
                $user->syncImages($images);
            }

            if ($accountForm->validate()) {
                $user->attributes = $accountForm->attributes;
                $user->save();

                $userSettings->attributes = $accountForm->attributes;
                $userSettings->save();

                $user->syncCategories(is_array($accountForm->categories) ? $accountForm->categories : []);

                $avatar = $accountForm->uploadAvatar();
                if ($avatar) {
                    $user->link('avatar', $avatar);
                    $accountForm->avatar = null;
                }
            }
        }

        return $this->render('account', [
            'accountForm' => $accountForm,
            'categories' => $categories,
            'cities' => $cities,
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
