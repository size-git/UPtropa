<?php
require_once __DIR__ . '/../bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('frontend/login.php');
}

$errors = Validator::login($_POST);
if (!$errors && !Auth::attempt(trim($_POST['login']), $_POST['password'])) {
    $errors['form'] = 'Неверный логин или пароль';
}
if ($errors) {
    back_with_errors('frontend/login.php', $errors, $_POST);
}

redirect(Auth::isAdmin() ? 'frontend/admin.php' : 'frontend/applications.php');
