<?php
/* @var $model User */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<main class="page-main">
    <div class="main-container page-container">
        <section class="new-task">
            <div class="new-task__wrapper">
                <h1>Новые задания</h1>
                <?php foreach ($tasks as $task): ?>
                    <div class="new-task__card">
                        <div class="new-task__title">
                            <a href="/task/view/<?= $task->id; ?>" class="link-regular">
                                <h2><?= $task['name']; ?></h2>
                            </a>
                            <a  class="new-task__type link-regular" href="#">
                                <p><?= $task->category->name; ?></p>
                            </a>
                        </div>
                        <div class="new-task__icon new-task__icon--<?= $task->category->icon;?>"></div>
                        <p class="new-task_description">
                            <?= $task['description']; ?>
                        </p>
                        <b class="new-task__price new-task__price--translation">
                            <?= $task['budget']; ?><b> ₽</b>
                        </b>
                        <p class="new-task__place"><?= $task['address']; ?></p>
                        <span class="new-task__time">
                            <?= \Yii::$app->formatter->asRelativeTime(strtotime($task['created_at'])); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="new-task__pagination">
                <ul class="new-task__pagination-list">
                    <li class="pagination__item"><a href="#"></a></li>
                    <li class="pagination__item pagination__item--current">
                        <a>1</a></li>
                    <li class="pagination__item"><a href="#">2</a></li>
                    <li class="pagination__item"><a href="#">3</a></li>
                    <li class="pagination__item"><a href="#"></a></li>
                </ul>
            </div>
        </section>
        <section  class="search-task">
            <div class="search-task__wrapper">

                <?php
                $form = ActiveForm::begin([
                    'id' => 'search-task__form',
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
                        $categories,
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
                        )->input('checkbox', [
                            'class' => 'visually-hidden checkbox__input',
                            'checked' => $taskFilter->$attr,
                        ]);
                    }
                    ?>
                </fieldset>
                <?= $form->field(
                    $taskFilter,
                    'date',
                    ['template' => '{label}<br>{input}{error}', 'options' => ['tag' => false]]
                )->dropDownList([
                    'day' => 'За день',
                    'week' => 'За неделю',
                    'month' => 'За месяц',
                    'year' => 'За год',
                ], [
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
