<?php
require_once __DIR__ . '/../bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('frontend/register.php');
}

$errors = Validator::register($_POST);
if ($errors) {
    back_with_errors('frontend/register.php', $errors, $_POST);
}

User::create($_POST);
flash('success', 'Вы зарегистрированы. Теперь войдите.');
redirect('frontend/login.php');
