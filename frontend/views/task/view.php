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
                        <button class=" button button__big-color response-button"
                                type="button">Откликнуться</button>
                        <button class="button button__big-color refusal-button"
                                type="button">Отказаться</button>
                        <button class="button button__big-color connection-button"
                                type="button">Написать сообщение</button>
                    </div>
                </div>
                <div class="content-view__feedback">
                    <h2>Отклики <span><?= $task->getReplies()->count(); ?></span></h2>
                    <div class="content-view__feedback-wrapper">

                        <?php foreach ($replies as $reply): ?>

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
                                <button class="button__small-color response-button button"
                                        type="button">Откликнуться</button>
                                <button class="button__small-color refusal-button button"
                                        type="button">Отказаться</button>
                                <button class="button__chat button"
                                        type="button"></button>
                            </div>
                        </div>
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
                                <?= \Yii::t(
                                    'app',
                                    '{n, plural, one{# заказ} few{# заказа} many{# заказов} other{# заказов}}',
                                    ['n' => $client->getClientTasks()->count()]
                                ); ?>
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
    <section class="modal response-form form-modal">
        <h2>Отклик на задание</h2>
        <form action="#" method="post">
            <p>
                <label class="form-modal-description" for="response-payment">Ваша цена</label>
                <input class="response-form-payment input input-middle input-money" type="text" name="response-payment" id="response-payment">
            </p>
            <p>
                <label class="form-modal-description" for="response-comment">Комментарий</label>
                <textarea class="input textarea" rows="4" id="response-comment" name="response-comment" placeholder="Place your text"></textarea>
            </p>
            <button class="button modal-button" type="submit">Отправить</button>
        </form>
        <button class="form-modal-close" type="button">Закрыть</button>
    </section>
    <section class="modal completion-form form-modal">
        <h2>Завершение задания</h2>
        <p class="form-modal-description">Задание выполнено?</p>
        <form action="#" method="post">
            <input class="visually-hidden completion-input completion-input--yes" type="radio" id="completion-radio--yes" name="completion" value="yes">
            <label class="completion-label completion-label--yes" for="completion-radio--yes">Да</label>
            <input class="visually-hidden completion-input completion-input--difficult" type="radio" id="completion-radio--yet" name="completion" value="difficulties">
            <label  class="completion-label completion-label--difficult" for="completion-radio--yet">Возникли проблемы</label>
            <p>
                <label class="form-modal-description" for="completion-comment">Комментарий</label>
                <textarea class="input textarea" rows="4" id="completion-comment" name="completion-comment" placeholder="Place your text"></textarea>
            </p>
            <p class="form-modal-description">
                Оценка
            <div class="feedback-card__top--name completion-form-star">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span class="star-disabled"></span>
            </div>
            </p>
            <button class="button modal-button" type="submit">Отправить</button>
        </form>
        <button class="form-modal-close" type="button">Закрыть</button>
    </section>
    <section class="modal form-modal refusal-form">
        <h2>Отказ от задания</h2>
        <p>
            Вы собираетесь отказаться от выполнения задания.
            Это действие приведёт к снижению вашего рейтинга.
            Вы уверены?
        </p>
        <button class="button__form-modal button"
                type="button">Отмена</button>
        <button class="button__form-modal refusal-button button"
                type="button">Отказаться</button>
        <button class="form-modal-close" type="button">Закрыть</button>
    </section>
</div>
<div class="overlay"></div>
