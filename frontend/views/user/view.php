<?php

use frontend\models\Status;
use yii\helpers\Url;

?>
<main class="page-main">
    <div class="main-container page-container">
        <section class="content-view">
            <div class="user__card-wrapper">
                <div class="user__card">
                    <img src="<?= $user->avatar ? Url::to([$user->avatar->getUrl()]) : '/img/user-photo.png'; ?>" width="120" height="120" alt="Аватар пользователя">
                    <div class="content-view__headline">
                        <h1><?= $user->last_name; ?> <?= $user->name; ?></h1>
                        <p><?= $user->address; ?>, <?= $user->age; ?> лет</p>
                        <div class="profile-mini__name five-stars__rate">
                            <?php $rating = $user->getRating(); ?>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="<?= $i <= $rating ? : 'star-disabled'; ?>"></span>
                            <?php endfor; ?>
                            <b><?= $rating; ?></b>
                        </div>
                        <b class="done-task">Выполнил <?= $user->getExecutorTasks()->where(['task_status_id' => Status::STATUS_DONE])->count(); ?> заказов</b>
                        <b class="done-review">Получил <?= $user->getOpinions()->count(); ?> отзывов</b>
                    </div>
                    <div class="content-view__headline user__card-bookmark user__card-bookmark--current">
                        <span>Был на сайте <?= explode(',', \Yii::$app->formatter->asDuration(time() - strtotime($user->last_activity_at)))[0]; ?> назад</span>
                        <a href="<?= Url::to(['/users/favorite/' . $user->id]); ?>"><b <?= !$favorite ?: 'style="background-color: red"'; ?>></b></a>
                    </div>
                </div>
                <div class="content-view__description">
                    <p><?= $user->about; ?></p>
                </div>
                <div class="user__card-general-information">
                    <div class="user__card-info">
                        <h3 class="content-view__h3">Специализации</h3>
                        <div class="link-specialization">
                            <?php foreach ($user->categories as $category): ?>
                                <a href="#" class="link-regular"><?= $category->name ?></a>
                            <?php endforeach; ?>
                        </div>
                        <h3 class="content-view__h3">Контакты</h3>
                        <div class="user__card-link">
                            <a class="user__card-link--tel link-regular" href="#"><?= $user->phone; ?></a>
                            <a class="user__card-link--email link-regular" href="#"><?= $user->email; ?></a>
                            <a class="user__card-link--skype link-regular" href="#"><?= $user->skype; ?></a>
                        </div>
                    </div>
                    <div class="user__card-photo">
                        <h3 class="content-view__h3">Фото работ</h3>
                        <?php foreach ($user->photos as $image): ?>
                            <a href="#"><img src="<?= Url::to([$image->getUrl()]); ?>" width="85" height="86" alt="Фото работы"></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="content-view__feedback">
                <h2>Отзывы<span>(<?= $user->getOpinions()->count(); ?>)</span></h2>
                <div class="content-view__feedback-wrapper reviews-wrapper">
                    <?php foreach ($user->opinions as $opinion): ?>
                        <div class="feedback-card__reviews">
                        <p class="link-task link">Задание <a href="#" class="link-regular">«<?= $opinion->task->name; ?>»</a></p>
                        <div class="card__review">
                            <a href="<?= Url::to(['/users/view/' . $opinion->author->id]); ?>"><img src="<?= $opinion->author->avatar ? Url::to([$opinion->author->avatar->getUrl()]) : '/img/man-glasses.jpg'; ?>" width="55" height="54"></a>
                            <div class="feedback-card__reviews-content">
                                <p class="link-name link"><a href="<?= Url::to(['/users/view/' . $opinion->author->id]); ?>" class="link-regular"><?= $opinion->author->last_name; ?> <?= $opinion->author->name; ?></a></p>
                                <p class="review-text">
                                    <?= $opinion->comment; ?>
                                </p>
                            </div>
                            <div class="card__review-rate">
                                <?php
                                switch ($opinion->rate) {
                                    case 1:
                                    case 2:
                                    case 3:
                                        $rateClass = 'three-rate';
                                        break;
                                    case 4:
                                    case 5:
                                        $rateClass = 'five-rate';
                                        break;
                                }
                                ?>
                                <p class="<?= $rateClass; ?> big-rate"><?= $opinion->rate; ?><span></span></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <section class="connect-desk">
            <div class="connect-desk__chat">

            </div>
        </section>
    </div>
</main>
