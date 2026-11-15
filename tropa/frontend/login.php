<?php
require_once __DIR__ . '/../backend/bootstrap.php';
if (Auth::isAdmin()) {
    redirect('frontend/admin.php');
}
if (Auth::isUser()) {
    redirect('frontend/applications.php');
}
$errors = pull('errors') ?? [];
$old = pull('old') ?? [];
$pageTitle = 'Вход';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
    <form class="card form" method="post" action="<?= url('backend/actions/login.php') ?>" data-validate novalidate>
        <h1>Авторизация</h1>

        <?php if (!empty($errors['form'])): ?>
            <div class="alert"><?= e($errors['form']) ?></div>
        <?php endif; ?>

        <label for="login">Логин</label>
        <input id="login" name="login" type="text" value="<?= e($old['login'] ?? '') ?>" autocomplete="username"
               required data-msg="Введите логин" class="<?= isset($errors['login']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="login"><?= e($errors['login'] ?? '') ?></div>

        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" autocomplete="current-password"
               required data-msg="Введите пароль" class="<?= isset($errors['password']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="password"><?= e($errors['password'] ?? '') ?></div>

        <button class="btn block" type="submit">Войти</button>
        <p class="switch"><a href="<?= url('frontend/register.php') ?>">Еще не зарегистрированы? Регистрация</a></p>
    </form>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
