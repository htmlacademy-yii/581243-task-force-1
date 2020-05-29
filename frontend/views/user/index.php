<?php

use frontend\models\Status;
use frontend\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

?>
<main class="page-main">
    <div class="main-container page-container">
        <section class="user__search">
            <div class="user__search-link">
                <p>Сортировать по:</p>
                <ul class="user__search-list">
                    <li class="user__search-item user__search-item--current">
                        <a href="<?= Url::to(['/users', 'sort_by' => User::RATING]); ?>" class="link-regular">Рейтингу</a>
                    </li>
                    <li class="user__search-item">
                        <a href="<?= Url::to(['/users', 'sort_by' => User::ORDERS]); ?>" class="link-regular">Числу заказов</a>
                    </li>
                    <li class="user__search-item">
                        <a href="<?= Url::to(['/users', 'sort_by' => User::VIEWS]); ?>" class="link-regular">Популярности</a>
                    </li>
                </ul>
            </div>
            <?php foreach ($dataProvider->getModels() as $user): ?>
                <div class="content-view__feedback-card user__search-wrapper">
                    <div class="feedback-card__top">
                        <div class="user__search-icon">
                            <a href="<?= Url::to(['/users/view/' . $user->id]); ?>"><img src="<?= $user->avatar ? Url::to([$user->avatar->getUrl()]) : '/img/user-photo.png'; ?>" width="65" height="65"></a>
                            <span><?= $user->getExecutorTasks()->where(['task_status_id' => Status::STATUS_DONE])->count(); ?> заданий</span>
                            <span><?= $user->getOpinions()->count(); ?> отзывов</span>
                        </div>
                        <div class="feedback-card__top--name user__search-card">
                            <p class="link-name"><a href="<?= Url::to(['/users/view/' . $user->id]); ?>" class="link-regular"><?= htmlspecialchars($user->last_name); ?> <?= htmlspecialchars($user->name); ?></a></p>
                            <?php $rating = $user->getRating(); ?>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="<?= $i <= $rating ? : 'star-disabled'; ?>"></span>
                            <?php endfor; ?>
                            <b><?= $rating; ?></b>
                            <p class="user__search-content">
                                <?= htmlspecialchars($user->about); ?>
                            </p>
                        </div>
                        <span class="new-task__time">
                            Был на сайте <?= explode(',', \Yii::$app->formatter->asDuration(time() - strtotime($user->last_activity_at)))[0]; ?> назад
                        </span>
                    </div>
                    <div class="link-specialization user__search-link--bottom">
                        <?php foreach ($user->categories as $category): ?>
                        <a href="#" class="link-regular"><?= $category->name ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="new-task__pagination">
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->getPagination(),
                    'linkContainerOptions' => ['class' => 'pagination__item'],
                    'options' => [
                        'class' => 'new-task__pagination-list',
                    ],
                    'prevPageLabel' => '&nbsp;',
                    'nextPageLabel' => '&nbsp;',
                ]); ?>
        </section>
        <section  class="search-task">
            <div class="search-task__wrapper">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'search-user__form',
                    'options' => [
                        'class' => 'search-task__form',
                        'name' => 'test',
                    ]
                ]);
                ?>

                <fieldset class="search-task__categories">
                    <legend><?= $userFilter->attributeLabels()['categories']; ?></legend>
                    <?= $form->field(
                        $userFilter,
                        'categories',
                        ['template' => '{input}{label}{error}', 'options' => ['tag' => false]]
                    )->checkboxList(
                        $categoriesProvider->getModels(),
                        ['item' =>  function ($index, $category, $name) use ($userFilter) {
                            return Html::checkbox(
                                    $name,
                                    in_array($category->id, $userFilter->categories), [
                                    'value' => $category->id,
                                    'id' => 'userfilter-categories_' . $index,
                                    'class' => 'visually-hidden checkbox__input',
                                ]) .
                                Html::label($category->name, 'userfilter-categories_' . $index);
                        }]
                    ); ?>
                </fieldset>

                <fieldset class="search-task__categories">
                    <legend>Дополнительно</legend>
                    <?php
                    foreach (['free', 'online', 'has_rate', 'favourite'] as $attr) {
                        echo $form->field(
                            $userFilter,
                            $attr,
                            ['template' => '{input}{label}{error}']
                        )->input('checkbox', [
                            'class' => 'visually-hidden checkbox__input',
                            'checked' => $userFilter->$attr,
                        ]);
                    }
                    ?>
                </fieldset>

                <?= $form->field(
                    $userFilter,
                    'name',
                    ['template' => '{label}<br>{input}{error}', 'options' => ['tag' => false]]
                )->input(
                    'search', [
                    'class' => 'input-middle input',
                ])->label(null, ['class' => 'search-task__name']); ?>

                <?= Html::submitButton('Искать', ['class' => 'button']); ?>

                <?php ActiveForm::end(); ?>
            </div>
        </section>
    </div>
</main>
