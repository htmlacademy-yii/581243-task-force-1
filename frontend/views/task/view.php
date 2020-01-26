<?php
use TaskForce\classes\actions\AvailableActions;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use TaskForce\classes\actions\RejectAction;
?>
<div class="table-layout">
    <main class="page-main">
        <div class="main-container page-container">
            <section class="content-view">
                <div class="content-view__card">
                    <div class="content-view__card-wrapper">
                        <div class="content-view__header">
                            <div class="content-view__headline">
                                <h1><?= $task->name; ?></h1>
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
                                <?= $task->description; ?>
                            </p>
                        </div>
                        <div class="content-view__attach">
                            <h3 class="content-view__h3">Вложения</h3>
                            <?php foreach ($task->files as $file): ?>
                                <a href="/file/download/<?= $file->id; ?>"><?= $file->title; ?> . <?= $file->type; ?></a>
                            <?php endforeach; ?>
                        </div>
                        <div class="content-view__location">
                            <h3 class="content-view__h3">Расположение</h3>
                            <div class="content-view__location-wrapper">
                                <div class="content-view__map">
                                    <a href="#"><img src="/img/map.jpg" width="361" height="292"
                                                     alt="Москва, Новый арбат, 23 к. 1"></a>
                                </div>
                                <div class="content-view__address">
                                    <span class="address__town">Москва</span><br>
                                    <span>Новый арбат, 23 к. 1</span>
                                    <p>Вход под арку, код домофона 1122</p>
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
                    </div>
                </div>
                <div class="content-view__feedback">
                    <h2>Отклики <span><?= ($task->client_id !== $user->id) ? '' : $task->getReplies()->count(); ?></span></h2>
                    <div class="content-view__feedback-wrapper">
                        <?php foreach ($replies as $reply): ?>
                            <?php if (in_array($user->id, [$task->client_id, $reply->executor_id])): ?>
                                <div class="content-view__feedback-card">
                                    <div class="feedback-card__top">
                                        <a href="/user/view/<?= $reply->executor->id; ?>">
                                            <img src="<?= $reply->executor->avatar->path ?? '/img/user-man.jpg' ?>"
                                                 width="55" height="55">
                                        </a>
                                        <div class="feedback-card__top--name">
                                            <p>
                                                <a href="/user/view/<?= $reply->executor->id; ?>" class="link-regular">
                                                    <?= $reply->executor->last_name; ?> <?= $reply->executor->name; ?>
                                                </a>
                                            </p>
                                            <?php $rating = $client->getRating(); ?>
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
                                        <?php if (in_array(AvailableActions::ACTION_TAKE_IN_WORK, $actions)): ?>
                                            <a href="/reply/take-in-work/<?= $task->id; ?>/<?= $reply->id; ?>" class="button__small-color request-button button"
                                               type="button">Подтвердить</a>
                                            <?php if (RejectAction::checkRights($user, $task, $reply)): ?>
                                                <a href="/reply/reject/<?= $task->id; ?>/<?= $reply->id; ?>" class="button__small-color refusal-button button"
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
                        <h3>Заказчик</h3>
                        <div class="profile-mini__top">
                            <img src="/img/man-brune.jpg" width="62" height="62" alt="Аватар заказчика">
                            <div class="profile-mini__name five-stars__rate">
                                <?php $rating = $client->getRating(); ?>
                                <p><?= $client->last_name; ?> <?= $client->name; ?></p>
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?= $i <= $rating ? : 'star-disabled'; ?>"></span>
                                <?php endfor; ?>
                                <b><?= $client->getRating(); ?></b>
                            </div>
                        </div>
                        <p class="info-customer">
                            <span>
                                <?= \Yii::t(
                                    'app',
                                    '{n, plural, one{# отзыв} few{# отзыва} many{# отзывов} other{# отзывов}}',
                                    ['n' => $client->getOpinions()->count()]
                                ); ?>
                            </span>
                            <span class="last-">
                                <?= explode(',', \Yii::$app->formatter->asDuration(time() - strtotime($client->created_at)))[0]; ?> на сайте
                            </span>
                        </p>
                        <a href="/user/view/<?= $client->id; ?>"" class="link-regular">Смотреть профиль</a>
                    </div>
                </div>
                <div class="connect-desk__chat">
                    <h3>Переписка</h3>
                    <div class="chat__overflow">
                        <div class="chat__message chat__message--out">
                            <p class="chat__message-time">10.05.2019, 14:56</p>
                            <p class="chat__message-text">Привет. Во сколько сможешь
                                приступить к работе?</p>
                        </div>
                        <div class="chat__message chat__message--in">
                            <p class="chat__message-time">10.05.2019, 14:57</p>
                            <p class="chat__message-text">На задание
                                выделены всего сутки, так что через час</p>
                        </div>
                        <div class="chat__message chat__message--out">
                            <p class="chat__message-time">10.05.2019, 14:57</p>
                            <p class="chat__message-text">Хорошо. Думаю, мы справимся</p>
                        </div>
                    </div>
                    <p class="chat__your-message">Ваше сообщение</p>
                    <form class="chat__form">
                        <textarea class="input textarea textarea-chat" rows="2" name="message-text" placeholder="Текст сообщения"></textarea>
                        <button class="button chat__button" type="submit">Отправить</button>
                    </form>
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
                'id' => 'response-comment',
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
                        'id' => 'response-comment',
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
