<?php
require_once __DIR__ . '/../backend/bootstrap.php';
if (Auth::isUser() || Auth::isAdmin()) {
    redirect('index.php');
}
$errors = pull('errors') ?? [];
$old = pull('old') ?? [];
$pageTitle = 'Регистрация';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
    <form class="card form" method="post" action="<?= url('backend/actions/register.php') ?>" data-validate novalidate>
        <h1>Создать пользователя</h1>

        <label for="login">Логин</label>
        <input id="login" name="login" type="text" value="<?= e($old['login'] ?? '') ?>" autocomplete="username"
               required pattern="[A-Za-z0-9]{6,}" data-msg="Только латиница и цифры, не менее 6 символов"
               class="<?= isset($errors['login']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="login"><?= e($errors['login'] ?? '') ?></div>

        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" autocomplete="new-password"
               required minlength="8" data-msg="Пароль — не менее 8 символов"
               class="<?= isset($errors['password']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="password"><?= e($errors['password'] ?? '') ?></div>

        <label for="full_name">ФИО</label>
        <input id="full_name" name="full_name" type="text" value="<?= e($old['full_name'] ?? '') ?>" autocomplete="name"
               required pattern="[А-Яа-яЁё\s]+" data-msg="Только кириллица и пробелы"
               class="<?= isset($errors['full_name']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="full_name"><?= e($errors['full_name'] ?? '') ?></div>

        <label for="phone">Телефон</label>
        <input id="phone" name="phone" type="tel" value="<?= e($old['phone'] ?? '') ?>" placeholder="8(999)123-45-67" autocomplete="tel"
               required pattern="8\(\d{3}\)\d{3}-\d{2}-\d{2}" data-msg="Формат: 8(999)123-45-67"
               class="<?= isset($errors['phone']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="phone"><?= e($errors['phone'] ?? '') ?></div>

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email"
               required data-msg="Введите корректный e-mail"
               class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="email"><?= e($errors['email'] ?? '') ?></div>

        <button class="btn block" type="submit">Зарегистрироваться</button>
        <p class="switch"><a href="<?= url('frontend/login.php') ?>">Уже есть аккаунт? Авторизация</a></p>
    </form>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
