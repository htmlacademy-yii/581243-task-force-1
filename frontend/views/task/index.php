<?php
/* @var $model User */

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
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
                            <a href="#" class="link-regular">
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
                    //'enableAjaxValidation' => true, // for ajax
                    'options' => [
                        'class' => 'search-task__form',
                        'name' => 'test',
                    ]
                ]);
                ?>
                <fieldset class="search-task__categories">
                    <legend><?= $taskFilter->attributeLabels()['categories']; ?></legend>
                    <?php
                    ActiveForm:
                    ['enableAjaxValidation' => true];
                    $field = new ActiveField([
                        'model' => $taskFilter,
                        'template' => "{input}{label}{error}",
                        'attribute' => 'categories',
                        'form' => $form,
                    ]);
                    echo $field->checkboxList(
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
                    );
                    ?>
                </fieldset>
                <fieldset class="search-task__categories">
                    <legend>Дополнительно</legend>
                    <?php
                    foreach (['my_city', 'no_executor', 'no_address'] as $attr) {
                        $field = new ActiveField([
                            'model' => $taskFilter, 'template' => "{input}{label}{error}",
                            'attribute' => $attr,
                            'form' => $form,
                        ]);
                        echo $field->input('checkbox', [
                            'class' => 'visually-hidden checkbox__input',
                            'checked' => $taskFilter->$attr,
                        ]);
                    }
                    ?>
                </fieldset>
                <?php
                $field = new ActiveField([
                    'model' => $taskFilter,
                    'template' => "{label}<br>{input}{error}",
                    'attribute' => 'date',
                    'form' => $form,
                    'options' => ['tag' => false],
                ]);
                echo $field->dropDownList([
                    'day' => 'За день',
                    'week' => 'За неделю',
                    'month' => 'За месяц',
                    'year' => 'За год',
                ], [
                    'class' => 'multiple-select input',
                    'size' => 1
                ])->label('Период', ['class' => 'search-task__name']);

                $field = new ActiveField([
                    'model' => $taskFilter,
                    'template' => "{label}<br>{input}{error}",
                    'attribute' => 'title',
                    'form' => $form,
                    'options' => ['tag' => false],
                ]);
                echo $field->input(
                    'search', [
                    'class' => 'input-middle input',
                ])->label('ПОИСК ПО НАЗВАНИЮ', ['class' => 'search-task__name']);;

                echo Html::submitButton('Искать', ['class' => 'button']);

                ActiveForm::end(); ?>

            </div>
        </section>
    </div>
</main>
