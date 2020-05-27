<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->registerJsFile('https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@7.2.0/dist/js/autoComplete.min.js');
?>

<main class="page-main">
    <div class="main-container page-container">
        <section class="create__task">
            <h1>Публикация нового задания</h1>
            <?php
            $form = ActiveForm::begin([
                'id' => $taskForm->formName(),
                'options' => [
                    'class' => 'create__task-form form-create',
                    'enctype' => 'multipart/form-data',
                ],
                'errorSummaryCssClass' => 'warning-item warning-item--error',
                'validateOnChange' => true,
            ]);
            ?>
            <div class="create__task-main">
                <div>
                    <?= $form->field(
                            $taskForm,
                            'name',
                            [
                                'template' => '{label}{input}<span>Кратко опишите суть работы</span>{error}',
                                'options' => ['class' => 'create__task-form form-create'],
                            ]
                        )
                        ->textarea([
                            'class' => 'input textarea',
                            'rows' => 1,
                            'placeholder' => 'Повесить полку',
                        ]); ?>

                    <?= $form->field(
                            $taskForm,
                            'description',
                            [
                                'template' => '{label}{input}<span>Укажите все пожелания и детали, чтобы исполнителям было проще соориентироваться</span>{error}',
                                'options' => ['class' => 'create__task-form form-create'],
                            ]
                        )
                        ->textarea([
                            'class' => 'input textarea',
                            'rows' => 7,
                            'placeholder' => 'Place your text',
                        ]); ?>

                    <?= $form->field(
                        $taskForm,
                        'category_id',
                        [
                            'template' => '{label}{input}<span>Выберете категорию</span>{error}',
                            'options' => ['class' => 'create__task-form form-create'],
                        ]
                    )
                        ->dropDownList($categories->getModels(), [
                            'class' => 'multiple-select input multiple-select-big',
                            'size' => 1
                        ])
                        ->error(['tag' => 'span']); ?>

                    <?= $form->field(
                            $taskForm,
                            'files[]',
                            [
                                'template' => '{label}<span>Загрузите файлы, которые помогут исполнителю лучше выполнить или оценить работу</span>{input}{error}',
                                'options' => ['class' => 'create__task-form form-create'],
                            ]
                        )
                        ->fileInput([
                            'multiple' => true,
                            'class' => 'create__file',
                        ]); ?>

                    <?= $form->field(
                            $taskForm,
                            'address',
                            [
                                'template' => '{label}{input}<span>Укажите адрес исполнения, если задание требует присутствия</span>{error}',
                                'options' => ['class' => 'create__task-form form-create'],
                            ]
                        )
                        ->input('search', [
                            'class' => 'input-navigation input-middle input',
                            'id' => 'autoComplete',
                            'tabindex' => 1,
                            'placeholder' => 'Санкт-Петербург, Калининский район',
                        ]); ?>

                    <?= $form->field(
                        $taskForm,
                        'lat',
                        [
                            'template' => '{input}',
                        ]
                    )
                        ->hiddenInput([
                            'id' => 'lat',
                        ]); ?>
                    <?= $form->field(
                        $taskForm,
                        'long',
                        [
                            'template' => '{input}',
                        ]
                    )
                        ->hiddenInput([
                            'id' => 'long',
                        ]); ?>

                    <div class="create__price-time">
                        <?= $form->field(
                            $taskForm,
                            'budget',
                            [
                                'template' => '{label}{input}<span>Не заполняйте для оценки исполнителем{error}</span>',
                                'options' => ['class' => 'create__price-time--wrapper'],
                            ]
                        )
                            ->textarea([
                                'class' => 'input textarea',
                                'rows' => 1,
                                'placeholder' => '1000',
                            ]); ?>
                        <?= $form->field(
                            $taskForm,
                            'expire_at',
                            [
                                'template' => '{label}{input}<span>Укажите крайний срок исполнения</span>{error}',
                                'options' => ['class' => 'create__price-time--wrapper'],
                            ]
                        )
                            ->input('date', [
                                'class' => 'input-middle input input-date',
                                'placeholder' => '10.11, 15:00',
                            ]); ?>
                    </div>
                </div>

                <div class="create__warnings">
                    <div class="warning-item warning-item--advice">
                        <h2>Правила хорошего описания</h2>
                        <h3>Подробности</h3>
                        <p>Друзья, не используйте случайный<br>
                            контент – ни наш, ни чей-либо еще. Заполняйте свои
                            макеты, вайрфреймы, мокапы и прототипы реальным
                            содержимым.</p>
                        <h3>Файлы</h3>
                        <p>Если загружаете фотографии объекта, то убедитесь,
                            что всё в фокусе, а фото показывает объект со всех
                            ракурсов.</p>
                    </div>
                    <?= $form->errorSummary(
                        $taskForm,
                        ['header' => '<h2>Ошибки заполнения формы</h2>']
                    ); ?>
                </div>
            </div>
            <?= Html::submitButton('Опубликовать', ['class' => 'button']); ?>
            <?php ActiveForm::end(); ?>
        </section>
    </div>
</main>
