<?php
/* @var $model User */

use frontend\models\Task;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\LinkPager;

?>
<main class="page-main">
    <div class="main-container page-container">
        <section class="new-task">
            <div class="new-task__wrapper">
                <h1>Новые задания</h1>
                <?php foreach ($taskProvider->getModels() as $task): ?>
                    <div class="new-task__card">
                        <div class="new-task__title">
                            <a href="<?=Url::to(['/task/view/' . $task->id]); ?>" class="link-regular">
                                <h2><?= htmlspecialchars($task['name']); ?></h2>
                            </a>
                            <a  class="new-task__type link-regular" href="#">
                                <p><?= $task->category->name; ?></p>
                            </a>
                        </div>
                        <div class="new-task__icon new-task__icon--<?= $task->category->icon;?>"></div>
                        <p class="new-task_description">
                            <?= htmlspecialchars($task['description']); ?>
                        </p>
                        <b class="new-task__price new-task__price--translation">
                            <?= $task['budget']; ?><b> ₽</b>
                        </b>
                        <p class="new-task__place"><?= htmlspecialchars($task['address']); ?></p>
                        <span class="new-task__time">
                            <?= \Yii::$app->formatter->asRelativeTime(strtotime($task['created_at'])); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="new-task__pagination">
                <?= LinkPager::widget([
                    'pagination' => $taskProvider->getPagination(),
                    'linkContainerOptions' => ['class' => 'pagination__item'],
                    'options' => [
                        'class' => 'new-task__pagination-list',
                    ],
                    'prevPageLabel' => '&nbsp;',
                    'nextPageLabel' => '&nbsp;',
                ]); ?>
            </div>
        </section>
        <section  class="search-task">
            <div class="search-task__wrapper">

                <?php
                $form = ActiveForm::begin([
                    'id' => 'search-task__form',
                    'method' => 'get',
                    'options' => [
                        'class' => 'search-task__form',
                        'name' => 'test',
                    ]
                ]);
                ?>
                <fieldset class="search-task__categories">
                    <legend><?= $taskFilter->attributeLabels()['categories']; ?></legend>
                    <?= $form->field(
                        $taskFilter,
                        'categories',
                        ['template' => '{input}{label}{error}', 'options' => ['tag' => false]]
                    )->checkboxList(
                        $categoriesProvider->getModels(),
                        ['item' =>  function ($index, $category, $name) use ($taskFilter) {
                            return Html::checkbox(
                                $name,
                                in_array($category->id, $taskFilter->categories), [
                                    'value' => $category->id,
                                    'id' => 'taskfilter-categories_' . $index,
                                    'class' => 'visually-hidden checkbox__input',
                                ]) .
                                Html::label($category->name, 'taskfilter-categories_' . $index);
                        }]
                    ); ?>
                </fieldset>
                <fieldset class="search-task__categories">
                    <legend>Дополнительно</legend>
                    <?php
                    foreach (['my_city', 'no_executor', 'no_address'] as $attr) {
                        echo $form->field(
                            $taskFilter,
                            $attr,
                            ['template' => '{input}{label}{error}']
                        )->checkbox(['class' => 'visually-hidden checkbox__input'], false);
                    }
                    ?>
                </fieldset>
                <?= $form->field(
                    $taskFilter,
                    'date',
                    ['template' => '{label}<br>{input}{error}', 'options' => ['tag' => false]]
                )->dropDownList(Task::getPeriods(), [
                    'class' => 'multiple-select input',
                    'size' => 1
                ])->label('Период', ['class' => 'search-task__name']); ?>

                <?= $form->field(
                    $taskFilter,
                    'title',
                    ['template' => '{label}<br>{input}{error}', 'options' => ['tag' => false]]
                )->input(
                    'search', [
                    'class' => 'input-middle input',
                ])->label('ПОИСК ПО НАЗВАНИЮ', ['class' => 'search-task__name']);;

                echo Html::submitButton('Искать', ['class' => 'button']);

                ActiveForm::end(); ?>

            </div>
        </section>
    </div>
</main>
