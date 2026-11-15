<?php
// Подключается первой строкой в каждой странице и обработчике.
session_start();

spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

function config(string $key)
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/config.php';
    }
    return $cfg[$key];
}

// Путь до корня проекта относительно сайта: '' для `php -S` из корня, '/tropa' для XAMPP.
function base_url(): string
{
    $root = str_replace('\\', '/', (string)realpath(__DIR__ . '/..'));
    $doc  = str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    if ($doc !== '' && strpos($root, $doc) === 0) {
        return rtrim(substr($root, strlen($doc)), '/');
    }
    return '';
}

function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $text): void
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

// Достаёт значение из сессии и сразу удаляет (flash, errors, old).
function pull(string $key)
{
    $value = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);
    return $value;
}

// Вернуть пользователя на форму с ошибками и введёнными значениями.
function back_with_errors(string $page, array $errors, array $old): void
{
    unset($old['password']);
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = $old;
    redirect($page);
}

function format_date(string $iso): string
{
    return date('d.m.Y', strtotime($iso));
}

function format_money(int $rub): string
{
    return number_format($rub, 0, '', ' ') . ' ₽';
}

// Длина строки в символах (кириллица считается правильно, расширение mbstring не обязательно).
function text_length(string $s): int
{
    return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : (int)preg_match_all('/./us', $s);
}
