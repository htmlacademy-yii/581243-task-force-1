<?php

namespace frontend\components;

use frontend\models\Category;
use frontend\models\Status;
use frontend\models\User;
use frontend\models\UserFilter;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Query;

class UserComponent
{
    /**
     * @param User $user
     * @param array $ids
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function syncCategories(User $user, array $ids): array
    {
        (new Query())
            ->createCommand()
            ->delete(
                'user_category',
                ['AND', ['user_id' => $user->id], ['not in', 'category_id', $ids]]
            )
            ->execute();
        foreach (Category::find()->where(['in', 'id',  $ids])
                     ->andWhere(['not in', 'id', $user->getCategories()->select('id')->column()])
                     ->all() as $category) {
            $user->link('categories', $category);
        }

        $user->user_status = $user->getCategories()->count() > 0 ? User::ROLE_EXECUTOR : User::ROLE_CLIENT;
        $user->save();

        return $user->categories;
    }

    /**
     * @param User $user
     * @param array $images
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function syncImages(User $user, array $images): void
    {
        /**
         * Удаляем все старые фотографии пользователя
         */
        $oldImages = $user->getPhotos()->select('id')->column();
        (new Query)
            ->createCommand()
            ->delete('user_photo', ['user_id' => $user->id])
            ->execute();

        (new Query)
            ->createCommand()
            ->delete('files', ['in', 'id', $oldImages])
            ->execute();

        /**
         * Добавляем новые фотографии (6 штук)
         */
        foreach (array_slice($images, 0, 6) as $image) {
            $user->link('photos', $image);
        }
    }

    /**
     * @param ActiveQuery $builder
     * @param UserFilter $filter
     * @return ActiveQuery
     */
    public function filter(ActiveQuery $builder, UserFilter $filter): ActiveQuery
    {
        $user = Yii::$app->user->identity;

        if (!empty($ids = $filter->categories)) {
            $builder
                ->joinWith('categories')
                ->andWhere(['in', 'categories.id', $ids]);
        }

        if ($filter->free) {
            $statuses = [Status::STATUS_IN_WORK];
            $builder->joinWith('executorTasks')
                ->andWhere(['not in', 'tasks.task_status_id', $statuses]);
        }

        if ($filter->online) {
            $date = date('Y-m-d H:i:s', strtotime('now - 30 minutes'));
            $builder->andWhere(['>=', 'last_activity_at', $date]);
        }

        if ($filter->has_rate) {
            $builder
                ->joinWith('opinions')
                ->groupBy(['users.id'])
                ->andFilterHaving(['>', 'count(opinions.id)', 0]);
        }

        if ($filter->favourite) {
            $builder->andWhere(['in', 'users.id', $user->getFavoriteUsers()->select('id')->column()]);
        }

        if (trim($filter->name)) {
            $builder->andWhere(['like', 'users.name', $filter->name]);
        }

        return $builder;
    }

    /**
     * @param ActiveQuery $builder
     * @param int $type
     * @return ActiveQuery
     */
    public function sortBy(ActiveQuery $builder, int $type): ActiveQuery
    {
        switch ($type) {
            case User::RATING:
                return $builder
                    ->joinWith('opinions')
                    ->groupBy(['users.id'])
                    ->orderBy(['SUM(opinions.rate) / COUNT(opinions.id)' => SORT_DESC]);
            case User::ORDERS:
                return $builder
                    ->joinWith('executorTasks')
                    ->groupBy(['users.id'])
                    ->orderBy(['COUNT(tasks.name)' => SORT_DESC]);
            case User::VIEWS:
                return $builder->orderBy(['views' => SORT_DESC]);
            default:
                return $builder;
        }
    }

    /**
     * @param UserFilter $userFilter
     * @return ActiveQuery
     */
    public function getList(UserFilter $userFilter): ActiveQuery
    {
        $usersBuilder = User::find()->where(['user_status' => User::ROLE_EXECUTOR])
            ->joinWith('userSettings')
            ->andWhere(['user_settings.hide_profile' => null]);

        if ($sort = Yii::$app->request->get('sort_by')) {
            $usersBuilder = $this->sortBy($usersBuilder, $sort);
        }

        $usersBuilder = $this->filter($usersBuilder, $userFilter);

        return $usersBuilder->distinct();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hideContacts(User $user): bool
    {
        $currentUserId = Yii::$app->user->id;
        if ($user->userSettings && $user->userSettings->show_only_client &&
            ($user->getExecutorTasks()
                    ->where(['client_id' => $currentUserId])
                    ->andWhere(['task_status_id' => Status::STATUS_IN_WORK])->count() == 0)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return User
     */
    public function view(User $user): User
    {
        $user->views++;
        $user->save();

        return $user;
    }
}
