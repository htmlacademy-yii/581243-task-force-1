<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\YiiAsset;
?>
<main class="page-main">
    <div class="main-container page-container">
        <section class="registration__user">
            <h1>Регистрация аккаунта</h1>
            <div class="registration-wrapper">

                <?php
                $form = ActiveForm::begin([
                    //'enableAjaxValidation' => true,
                    'id' => $user->formName(),
                    'options' => [
                        'class' => 'registration__user-form form-create',
                    ],
                ]);
                echo $form->errorSummary($user);
                ?>
                <?= $form->field(
                    $user,
                    'email',
                    ['template' => '{label}{input}', 'options' => ['tag' => false]]
                    )
                    ->textarea([
                        'class' => 'input textarea',
                        'rows' => 1,
                        'placeholder' => 'email'
                    ]);?>
                    <span>Введите валидный адрес электронной почты</span>

                <?= $form->field(
                    $user,
                    'name',
                    ['template' => '{label}{input}{error}', 'options' => ['tag' => false]]
                )
                    ->textarea([
                        'class' => 'input textarea',
                        'rows' => 1,
                        'placeholder' => 'Мамедов Кумар',
                    ])->label('Ваше имя');?>
                    <span>Введите ваше имя и фамилию</span>

                <?= $form->field(
                    $user,
                    'city_id',
                    ['template' => '{label}{input}{error}', 'options' => ['tag' => false]]
                )
                    ->dropDownList($cities, [
                        'class' => 'multiple-select input town-select registration-town',
                        'size' => 1
                    ])->label('Город проживания');?>
                    <span>Укажите город, чтобы находить подходящие задачи</span>

                <?= $form->field(
                    $user,
                    'password',
                    ['template' => '{label}{input}{error}', 'options' => ['tag' => false]]
                )
                    ->input('password', [
                        'class' => 'input textarea',
                        'rows' => 1,
                    ]);?>
                <span>Длина пароля от 8 символов</span>

                <?= Html::submitButton('Cоздать аккаунт', ['class' => 'button button__registration']); ?>
                <?php ActiveForm::end(); ?>

            </div>
        </section>

    </div>
</main>
