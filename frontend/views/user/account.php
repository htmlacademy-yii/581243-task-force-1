<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<main class="page-main">
    <div class="main-container page-container">
        <section class="account__redaction-wrapper">
            <h1>Редактирование настроек профиля</h1>
            <?php
            $form = ActiveForm::begin([
                'id' => $accountForm->formName(),
                'validateOnChange' => true,
            ]);
            ?>
                <div class="account__redaction-section">
                    <h3 class="div-line">Настройки аккаунта</h3>
                    <div class="account__redaction-section-wrapper">
                        <div class="account__redaction-avatar">
                            <img src="<?= $user->avatar ? Url::to([$user->avatar->getUrl()]) : '/img/man-glasses.jpg'; ?>" width="156" height="156">
                            <?= $form->field(
                                $accountForm,
                                'avatar',
                                [
                                    'template' => '{input}{label}{error}',
                                ]
                            )
                                ->fileInput([
                                    'id' => 'upload-avatar',
                                ])->label(null, ['class' => 'link-regular']); ?>
                        </div>
                        <div class="account__redaction">
                            <?= $form->field(
                                $accountForm,
                                'name',
                                ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input account__input--name']]
                            )
                                ->input('text', [
                                    'class' => 'input textarea',
                                    'placeholder' => 'Титов Денис'
                                ]); ?>
                            <?= $form->field(
                                $accountForm,
                                'email',
                                ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input account__input--email']]
                            )
                                ->input('email', [
                                    'class' => 'input textarea',
                                    'placeholder' => 'DenisT@bk.ru'
                                ]); ?>
                            <?= $form->field(
                                $accountForm,
                                'city_id',
                                [
                                    'template' => '{label}{input}{error}',
                                    'options' => ['class' => 'account__input account__input--name'],
                                ]
                            )
                                ->dropDownList($cities, [
                                    'class' => 'multiple-select input multiple-select-big',
                                    'size' => 1
                                ]); ?>
                            <?= $form->field(
                                $accountForm,
                                'birthday_at',
                                [
                                    'template' => '{label}{input}{error}',
                                    'options' => ['class' => 'account__input account__input--date'],
                                ]
                            )
                                ->input('date', [
                                    'class' => 'input-middle input input-date',
                                    'placeholder' => '15.08.1987',
                                ]); ?>
                            <?= $form->field(
                                $accountForm,
                                'about',
                                ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input account__input--info']]
                            )
                                ->textarea([
                                    'class' => 'input textarea',
                                    'rows' => 7,
                                    'placeholder' => 'Place your text',
                                ]); ?>
                        </div>
                    </div>
                    <h3 class="div-line">Выберите свои специализации</h3>
                    <div class="account__redaction-section-wrapper">
                        <?= $form->field(
                            $accountForm,
                            'categories',
                            ['template' => '{input}{label}{error}', 'options' => ['class' => 'search-task__categories account_checkbox--bottom']]
                        )->checkboxList(
                            $categories,
                            ['item' =>  function ($index, $category, $name) use ($user) {
                                return Html::checkbox(
                                        $name,
                                        in_array($category->id, $user->getCategories()->select('id')->column()), [
                                        'value' => $category->id,
                                        'id' => 'accountForm-categories_' . $index,
                                        'class' => 'visually-hidden checkbox__input',
                                    ]) .
                                    Html::label($category->name, 'accountForm-categories_' . $index);
                            }]
                        )->label(false); ?>
                    </div>
                    <h3 class="div-line">Безопасность</h3>
                    <div class="account__redaction-section-wrapper account__redaction">
                        <?= $form->field(
                            $accountForm,
                            'new_password',
                            ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input']]
                        )
                            ->input('password', [
                                'class' => 'input textarea',
                            ]); ?>
                        <?= $form->field(
                            $accountForm,
                            'confirm',
                            ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input']]
                        )
                            ->input('password', [
                                'class' => 'input textarea',
                            ]); ?>
                    </div>

                    <h3 class="div-line">Фото работ</h3>

                    <div class="account__redaction-section-wrapper account__redaction">
                        <span class="dropzone" id="myDropzone"></span>
                    </div>

                    <h3 class="div-line">Контакты</h3>
                    <div class="account__redaction-section-wrapper account__redaction">
                        <?= $form->field(
                            $accountForm,
                            'phone',
                            ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input']]
                        )
                            ->input('tel', [
                                'class' => 'input textarea',
                                'placeholder' => '8 (555) 187 44 87'
                            ]); ?>
                        <?= $form->field(
                            $accountForm,
                            'skype',
                            ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input']]
                        )
                            ->input('text', [
                                'class' => 'input textarea',
                                'placeholder' => 'DenisT'
                            ]); ?>
                        <?= $form->field(
                            $accountForm,
                            'messenger',
                            ['template' => '{label}{input}{error}', 'options' => ['class' => 'account__input']]
                        )
                            ->input('text', [
                                'class' => 'input textarea',
                                'placeholder' => '@DenisT'
                            ]); ?>
                    </div>
                    <h3 class="div-line">Настройки сайта</h3>
                    <h4>Уведомления</h4>
                    <div class="account__redaction-section-wrapper account_section--bottom">
                        <div class="search-task__categories account_checkbox--bottom">
                            <?= $form->field(
                                $accountForm,
                                'new_messages',
                                [
                                    'template' => '{input}{label}',
                                    'options' => ['tag' => false],
                                ]
                            )
                                ->input('checkbox', [
                                    'class' => 'visually-hidden checkbox__input',
                                    'checked' => $accountForm->new_messages,
                                    'value'=>1,
                                    'uncheckValue'=>0,
                                ]); ?>
                            <?= $form->field(
                                $accountForm,
                                'task_action',
                                [
                                    'template' => '{input}{label}',
                                    'options' => ['tag' => false],
                                ]
                            )
                                ->input('checkbox', [
                                    'class' => 'visually-hidden checkbox__input',
                                    'checked' => $accountForm->task_action,
                                    'value'=>1,
                                    'uncheckValue'=>0,
                                ]); ?>
                            <?= $form->field(
                                $accountForm,
                                'new_response',
                                [
                                    'template' => '{input}{label}',
                                    'options' => ['tag' => false],
                                ]
                            )
                                ->input('checkbox', [
                                    'class' => 'visually-hidden checkbox__input',
                                    'checked' => $accountForm->new_response,
                                    'value'=>1,
                                    'uncheckValue'=>0,
                                ]); ?>
                        </div>
                        <div class="search-task__categories account_checkbox account_checkbox--secrecy">
                            <?= $form->field(
                                $accountForm,
                                'show_only_client',
                                [
                                    'template' => '{input}{label}',
                                    'options' => ['tag' => false],
                                ]
                            )
                                ->input('checkbox', [
                                    'class' => 'visually-hidden checkbox__input',
                                    'checked' => $accountForm->show_only_client,
                                    'value'=>1,
                                    'uncheckValue'=>0,
                                ]); ?>
                            <?= $form->field(
                                $accountForm,
                                'hide_profile',
                                [
                                    'template' => '{input}{label}',
                                    'options' => ['tag' => false],
                                ]
                            )
                                ->input('checkbox', [
                                    'class' => 'visually-hidden checkbox__input',
                                    'checked' => $accountForm->hide_profile,
                                    'value'=>1,
                                    'uncheckValue'=>0,
                                ]); ?>
                        </div>
                    </div>
                </div>
            <?= Html::submitButton('Сохранить изменения', ['class' => 'button', 'id' => 'submit']); ?>
            <?php ActiveForm::end(); ?>
        </section>
    </div>
</main>
<script src="/js/dropzone.js"></script>
<script>
  Dropzone.autoDiscover = false;

  var dropzone = new Dropzone(".dropzone", {url: window.location.href, maxFiles: 6, uploadMultiple: true,
    acceptedFiles: 'image/*', previewTemplate: '<a href="#"><img data-dz-thumbnail alt="Фото работы"></a>',
    headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  }});

</script>
