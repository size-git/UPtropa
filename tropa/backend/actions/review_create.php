<?php
require_once __DIR__ . '/../bootstrap.php';
Auth::requireUser();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('frontend/applications.php');
}

$errors = Validator::review($_POST);
if (!$errors) {
    $error = Review::create(Auth::id(), (int)($_POST['application_id'] ?? 0), (int)$_POST['rating'], $_POST['text']);
    if ($error) {
        $errors['form'] = $error;
    }
}

if ($errors) {
    flash('error', implode('. ', $errors));
} else {
    flash('success', 'Спасибо за отзыв!');
}
redirect('frontend/applications.php');
