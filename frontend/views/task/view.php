<?php

use frontend\models\Status;
use frontend\models\User;
use TaskForce\actions\AvailableActions;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use TaskForce\actions\RejectAction;

$this->registerJsFile('https://api-maps.yandex.ru/2.1/?apikey=<?=' . \Yii::$app->params['apiKey'] . '&lang=ru_RU');

if (in_array($task->task_status_id, [
        Status::STATUS_IN_WORK,
        Status::STATUS_DONE,
        Status::STATUS_FAILED,
        Status::STATUS_EXPIRED,
    ]) && in_array($user->id, [$task->executor_id, $task->client_id])) {
    $this->registerJsFile('/js/messenger.js');
}
?>
<div class="table-layout">
    <main class="page-main">
        <div class="main-container page-container">
            <section class="content-view">
                <div class="content-view__card">
                    <div class="content-view__card-wrapper">
                        <div class="content-view__header">
                            <div class="content-view__headline">
                                <h1><?= htmlspecialchars($task->name); ?></h1>
                                <span>Размещено в категории
                                    <a href="#" class="link-regular"><?= $task->category->name; ?></a>
                                    <?= \Yii::$app->formatter->asRelativeTime(strtotime($task->created_at)); ?></span>
                            </div>
                            <b class="new-task__price new-task__price--clean content-view-price"><?= $task->budget; ?><b> ₽</b></b>
                            <div class="new-task__icon new-task__icon--<?= $task->category->icon;?> content-view-icon"></div>
                        </div>
                        <div class="content-view__description">
                            <h3 class="content-view__h3">Общее описание</h3>
                            <p>
                                <?= htmlspecialchars($task->description); ?>
                            </p>
                        </div>
                        <div class="content-view__attach">
                            <h3 class="content-view__h3">Вложения</h3>
                            <?php foreach ($task->files as $file): ?>
                                <a href="<?=Url::to(['/file/download/' . $file->id]); ?>"><?= $file->title; ?> . <?= $file->type; ?></a>
                            <?php endforeach; ?>
                        </div>
                        <div class="content-view__location">
                            <h3 class="content-view__h3">Расположение</h3>
                            <div class="content-view__location-wrapper">
                                <div id="map"
                                     style="width: 361px;
                                 height: 292px"
                                     data-lat="<?= $task->lat; ?>"
                                     data-long="<?= $task->long; ?>"></div>
                                <div class="content-view__address">
                                    <span class="address__town">
                                        <?= htmlspecialchars($task->address); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-view__action-buttons">
                        <?php if (in_array(AvailableActions::ACTION_RESPOND, $actions)): ?>
                            <button class=" button button__big-color response-button open-modal"
                                    type="button" data-for="response-form">Откликнуться</button>
                        <?php endif; ?>

                        <?php if (in_array(AvailableActions::ACTION_REFUSE, $actions)): ?>
                            <button class="button button__big-color refusal-button open-modal"
                                    type="button" data-for="refuse-form">Отказаться</button>
                        <?php endif; ?>
                        <?php if (in_array(AvailableActions::ACTION_DONE, $actions)): ?>
                            <button class="button button__big-color request-button open-modal"
                                    type="button" data-for="complete-form">Завершить</button>
                        <?php endif; ?>
                        <?php if (in_array(AvailableActions::ACTION_CANCEL, $actions)): ?>
                            <?= Html::a('Отмена', "/task/cancel/$task->id", [
                            'class' => 'button button__big-color refusal-button open-modal',
                            'data-for' => 'canceled-form',
                            ]); ?>

                        <?php endif; ?>
                    </div>
                </div>
                <div class="content-view__feedback">
                    <h2>Отклики <span><?= ($task->client_id !== $user->id) ? '' : $task->getReplies()->count(); ?></span></h2>
                    <div class="content-view__feedback-wrapper">
                        <?php foreach ($replies as $reply): ?>
                            <?php if (in_array($user->id, [$task->client_id, $reply->executor_id])): ?>
                                <div class="content-view__feedback-card">
                                    <div class="feedback-card__top">
                                        <a href="<?=Url::to(['/users/view/' . $reply->executor->id]); ?>">

                                            <img src="<?= $reply->executor->avatar ? Url::to([$reply->executor->avatar->getUrl()]) : '/img/user-man.jpg' ?>"
                                                 width="55" height="55">
                                        </a>
                                        <div class="feedback-card__top--name">
                                            <p>
                                                <a href="<?=Url::to(['/users/view/' . $reply->executor->id]); ?>" class="link-regular">
                                                    <?= htmlspecialchars($reply->executor->last_name); ?> <?= htmlspecialchars($reply->executor->name); ?>
                                                </a>
                                            </p>
                                            <?php $rating = $reply->executor->getRating(); ?>
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <span class="<?= $i <= $rating ? : 'star-disabled'; ?>"></span>
                                                <?php endfor; ?>
                                            <b><?= $reply->executor->getRating(); ?></b>
                                        </div>
                                        <span class="new-task__time"><?= \Yii::$app->formatter->asRelativeTime(strtotime($reply->created_at)); ?></span>
                                    </div>
                                    <div class="feedback-card__content">
                                        <p>
                                            <?= $reply->comment; ?>
                                        </p>
                                        <span><?= $reply->price; ?> ₽</span>
                                    </div>
                                    <div class="feedback-card__actions">
                                        <?php if (in_array(AvailableActions::ACTION_TAKE_IN_WORK, $actions) &&
                                            !$reply->rejected): ?>
                                            <a href="<?=Url::to(['/reply/take-in-work/' . $task->id . '/' . $reply->id]); ?>" class="button__small-color request-button button"
                                               type="button">Подтвердить</a>
                                            <?php if (RejectAction::checkRights($user, $task, $reply)): ?>
                                                <a href="<?=Url::to(['/reply/reject/' . $task->id . '/' . $reply->id]); ?>" class="button__small-color refusal-button button"
                                                   type="button">Отказать</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
            </section>
            <section class="connect-desk">
                <div class="connect-desk__profile-mini">
                    <div class="profile-mini__wrapper">
                        <h3><?= $viewer === User::ROLE_CLIENT ? 'Заказчик' : 'Исполнитель'; ?></h3>
                        <div class="profile-mini__top">
                            <img src="<?= $profile->avatar ? Url::to([$profile->avatar->getUrl()]) : '/img/man-brune.jpg'; ?>" width="62" height="62" alt="Аватар заказчика">
                            <div class="profile-mini__name five-stars__rate">
                                <?php $rating = $profile->getRating(); ?>
                                <p><?= htmlspecialchars($profile->last_name); ?> <?= htmlspecialchars($profile->name); ?></p>
                                <?php if ($viewer === User::ROLE_EXECUTOR): ?>
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?= $i <= $rating ? : 'star-disabled'; ?>"></span>
                                <?php endfor; ?>
                                <b><?= $profile->getRating(); ?></b>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="info-customer">
                            <span>
                                <?= \Yii::t(
                                    'app',
                                    '{n, plural, one{# отзыв} few{# отзыва} many{# отзывов} other{# отзывов}}',
                                    ['n' => $profile->getOpinions()->count()]
                                ); ?>
                            </span>
                            <span class="last-">
                                <?= explode(',', \Yii::$app->formatter->asDuration(time() - strtotime($profile->created_at)))[0]; ?> на сайте
                            </span>
                        </p>
                        <?php if ($viewer === User::ROLE_EXECUTOR): ?>
                            <a href="<?=Url::to(['/user/view/' . $profile->id]); ?>" class="link-regular">Смотреть профиль</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="chat-container">
                    <!--                    добавьте сюда атрибут task с указанием в нем id текущего задания-->
                    <chat class="connect-desk__chat" task="<?= $task->id; ?>"></chat>
                </div>
            </section>
        </div>
    </main>
    <section class="modal response-form form-modal" id="response-form">
        <h2>Отклик на задание</h2>
        <?php
        $form = ActiveForm::begin([
            'id' => $replyForm->formName(),
            'action' => '/reply/create',
        ]);
        ?>

        <?= $form->field(
            $replyForm,
            'price',
            [
                'template' => '{label}{input}{error}',
                'options' => ['class' => 'create__task-form form-create'],
            ]
        )
            ->input('text', [
                'class' => 'response-form-payment input input-middle input-money',
                'id' => 'response-payment',
            ])
            ->label('Ваша Цена', ['class' => 'form-modal-description']); ?>

        <?= $form->field(
            $replyForm,
            'comment',
            [
                'template' => '{label}{input}{error}',
                'options' => ['class' => 'create__task-form form-create'],
            ]
        )
            ->textarea([
                'class' => 'input textarea',
                'rows' => 4,
                'placeholder' => 'Place your text',
            ])
            ->label('Комментарий', ['class' => 'form-modal-description']); ?>

        <?= $form->field(
            $replyForm,
            'task_id',
            [
                'template' => '{input}',
                'options' => ['class' => 'create__task-form form-create'],
            ]
        )
            ->hiddenInput([
                'value' => $task->id,
            ]); ?>

        <?= Html::submitButton('Отправить', ['class' => 'button modal-button']); ?>
        <?php ActiveForm::end(); ?>
        <button class="form-modal-close" type="button">Закрыть</button>
    </section>
    <section class="modal completion-form form-modal" id="complete-form">
        <h2>Завершение задания</h2>
        <?php
        $form = ActiveForm::begin([
            'id' => $doneTaskForm->formName(),
            'action' => '/task/done',
        ]);
        ?>
        <p class="form-modal-description">Задание выполнено?</p>
        <?= $form->field(
            $doneTaskForm,
            'done',
            [
                'template' => '{input}{error}',
            ]
        )
            ->radioList([
                'yes' => 'Да',
                'difficult' => 'Возникли проблемы'
            ], [
                'item' => function ($index, $label, $name, $checked, $value) {
                    return Html::radio(
                            $name,
                            $value === 'yes',
                            [
                                'value' => $value,
                                'id' => 'completion-radio--' . ($value === 'yes' ? 'yes' : 'yet'),
                                'class' => 'visually-hidden completion-input completion-input--' . $value,
                                'name' => $name,
                            ]
                        ) .
                        Html::label(
                            $label,
                            'completion-radio--' . ($value === 'yes' ? 'yes' : 'yet'),
                            [
                                'class' => 'completion-label completion-label--' . $value,
                            ]
                        );
                },
                'tag' => false,
            ]);
        ?>
            <p>
                <?= $form->field(
                    $doneTaskForm,
                    'comment',
                    [
                        'template' => '{label}<br>{input}{error}',
                        'options' => [
                            'tag' => false,
                        ],
                    ]
                )
                    ->textarea([
                        'class' => 'input textarea',
                        'rows' => 4,
                        'placeholder' => 'Place your text',
                        'tag' => false,
                    ])
                    ->label(null, ['class' => 'form-modal-description']); ?>
            </p>
            <p class="form-modal-description">
                Оценка
            <div class="feedback-card__top--name completion-form-star">
                <span class="star-disabled"></span>
                <span class="star-disabled"></span>
                <span class="star-disabled"></span>
                <span class="star-disabled"></span>
                <span class="star-disabled"></span>
            </div>
            </p>
        <?= $form->field(
            $doneTaskForm,
            'rate',
            [
                'template' => '{input}',
            ]
        )
            ->hiddenInput([
                'id' => 'rating',
            ]); ?>
        <?= $form->field(
            $doneTaskForm,
            'task_id',
            [
                'template' => '{input}',
            ]
        )
            ->hiddenInput([
                'value' => $task->id,
            ]); ?>
        <?= Html::submitButton('Отправить', ['class' => 'button modal-button']); ?>
        <?php ActiveForm::end(); ?>
        <button class="form-modal-close" type="button">Закрыть</button>
    </section>
    <section class="modal form-modal refusal-form" id="refuse-form">
        <?php
        $form = ActiveForm::begin([
            'id' => $refuseTaskForm->formName(),
            'action' => '/task/refuse',
        ]);
        ?>
        <h2>Отказ от задания</h2>
        <p>
            Вы собираетесь отказаться от выполнения задания.
            Это действие приведёт к снижению вашего рейтинга.
            Вы уверены?
        </p>
        <?= $form->field(
            $refuseTaskForm,
            'task_id',
            [
                'template' => '{input}',
            ]
        )
            ->hiddenInput([
                'value' => $task->id,
            ]); ?>
        <button class="button__form-modal button" id="close-modal"
                type="button">Отмена</button>
        <?= Html::submitButton('Отказаться', ['class' => 'button__form-modal refusal-button button']); ?>
        <button class="form-modal-close" type="button">Закрыть</button>
        <?php ActiveForm::end(); ?>
    </section>
</div>
<div class="overlay"></div>
