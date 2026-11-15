<?php
// Перед подключением страница может задать $pageTitle.
$flash = pull('flash');
$pageTitle = $pageTitle ?? 'Тропа';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — Тропа</title>
    <link rel="icon" href="<?= url('frontend/assets/img/logo.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= url('frontend/assets/css/style.css') ?>">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a class="logo" href="<?= url('index.php') ?>">
            <img src="<?= url('frontend/assets/img/logo.svg') ?>" alt="" width="28" height="28">
            <span>Тропа</span>
        </a>
        <button class="menu-btn" type="button" data-menu-btn aria-expanded="false" aria-label="Меню">
            <span></span><span></span><span></span>
        </button>
        <nav class="menu" data-menu>
            <a href="<?= url('index.php') ?>">Экскурсии</a>
            <?php if (Auth::isAdmin()): ?>
                <a href="<?= url('frontend/admin.php') ?>">Панель администратора</a>
                <a href="<?= url('backend/actions/logout.php') ?>">Выйти</a>
            <?php elseif (Auth::isUser()): ?>
                <a href="<?= url('frontend/applications.php') ?>">Мои бронирования</a>
                <a href="<?= url('frontend/application_create.php') ?>">Забронировать</a>
                <a href="<?= url('backend/actions/logout.php') ?>">Выйти</a>
            <?php else: ?>
                <a href="<?= url('frontend/login.php') ?>">Вход</a>
                <a href="<?= url('frontend/register.php') ?>">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if ($flash): ?>
    <div class="toast <?= e($flash['type']) ?>" role="status"><?= e($flash['text']) ?></div>
<?php endif; ?>
