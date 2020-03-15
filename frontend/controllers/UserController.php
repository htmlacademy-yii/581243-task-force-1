<?php

namespace frontend\controllers;

use frontend\models\Category;
use frontend\models\City;
use frontend\models\LoginForm;
use frontend\models\User;
use frontend\models\UserFilter;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;

class UserController extends SecuredController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $userFilter = new UserFilter();
        $categories = Category::find()->all();

        $usersBuilder = User::find()->where(['user_status' => User::EXECUTOR])
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

        return $this->render('index', [
            'users' => $usersBuilder->all(),
            'userFilter' => $userFilter,
            'categories' => $categories,
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionShow($id)
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
    public function actionFavorite($id)
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
                return $this->redirect('/task/');
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
        $loginForm = new LoginForm();
        if (Yii::$app->request->getIsPost()) {
            $loginForm->load(Yii::$app->request->post());
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($loginForm);
            }
            if ($loginForm->validate()) {
                $user = $loginForm->getUser();
                Yii::$app->user->login($user);
                return $this->redirect('/task/');
            }
        }
    }

    /**
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->redirect('/');
    }
}
