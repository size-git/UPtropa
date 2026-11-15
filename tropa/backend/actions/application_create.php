<?php
require_once __DIR__ . '/../bootstrap.php';
Auth::requireUser();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('frontend/application_create.php');
}

$errors = Validator::application($_POST);
if ($errors) {
    back_with_errors('frontend/application_create.php', $errors, $_POST);
}

Application::create(Auth::id(), $_POST);
flash('success', 'Заявка отправлена администратору');
redirect('frontend/applications.php');
