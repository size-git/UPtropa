<?php
// Каждый метод возвращает массив ошибок вида ['поле' => 'текст']. Пустой массив — всё хорошо.
class Validator
{
    public static function register(array $d): array
    {
        $e = [];
        $login = trim($d['login'] ?? '');
        $pass  = (string)($d['password'] ?? '');
        $name  = trim($d['full_name'] ?? '');
        $phone = trim($d['phone'] ?? '');
        $email = trim($d['email'] ?? '');

        if ($login === '') {
            $e['login'] = 'Введите логин';
        } elseif (!preg_match('/^[A-Za-z0-9]{6,}$/', $login)) {
            $e['login'] = 'Только латиница и цифры, не менее 6 символов';
        } elseif (User::loginTaken($login)) {
            $e['login'] = 'Этот логин уже занят';
        }

        if ($pass === '') {
            $e['password'] = 'Введите пароль';
        } elseif (text_length($pass) < 8) {
            $e['password'] = 'Пароль — не менее 8 символов';
        }

        if ($name === '') {
            $e['full_name'] = 'Введите ФИО';
        } elseif (!preg_match('/^[А-Яа-яЁё\s]+$/u', $name)) {
            $e['full_name'] = 'Только кириллица и пробелы';
        }

        if ($phone === '') {
            $e['phone'] = 'Введите телефон';
        } elseif (!preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
            $e['phone'] = 'Формат: 8(999)123-45-67';
        }

        if ($email === '') {
            $e['email'] = 'Введите e-mail';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $e['email'] = 'Введите корректный e-mail';
        }
        return $e;
    }

    public static function login(array $d): array
    {
        $e = [];
        if (trim($d['login'] ?? '') === '') {
            $e['login'] = 'Введите логин';
        }
        if (($d['password'] ?? '') === '') {
            $e['password'] = 'Введите пароль';
        }
        return $e;
    }

    public static function application(array $d): array
    {
        $e = [];
        $excursion = Excursion::find((int)($d['excursion_id'] ?? 0));
        if (!$excursion) {
            $e['excursion_id'] = 'Выберите экскурсию из списка';
        }

        $dateStr = trim($d['tour_date'] ?? '');
        $date = null;
        if ($dateStr === '') {
            $e['tour_date'] = 'Введите дату';
        } elseif (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $dateStr)) {
            $e['tour_date'] = 'Формат даты: ДД.ММ.ГГГГ';
        } else {
            $date = DateTime::createFromFormat('d.m.Y', $dateStr);
            if (!$date || $date->format('d.m.Y') !== $dateStr) {
                $e['tour_date'] = 'Такой даты не существует';
                $date = null;
            } else {
                $date->setTime(0, 0);
                if ($date < new DateTime('today')) {
                    $e['tour_date'] = 'Дата не может быть в прошлом';
                } elseif ((int)$date->format('N') < 6) {
                    $e['tour_date'] = 'Экскурсии проходят только в субботу и воскресенье';
                }
            }
        }

        $countStr = trim((string)($d['participants'] ?? ''));
        if (!preg_match('/^\d+$/', $countStr) || (int)$countStr < 1 || (int)$countStr > 10) {
            $e['participants'] = 'Участников — от 1 до 10';
        }

        if (!Database::value('SELECT 1 FROM payment_methods WHERE id = ?', [(int)($d['payment_method_id'] ?? 0)])) {
            $e['payment_method_id'] = 'Выберите способ оплаты';
        }

        // Лимит мест проверяем, только когда остальное корректно.
        if (!$e) {
            $left = Application::seatsLeft((int)$excursion['id'], $date->format('Y-m-d'));
            if ((int)$countStr > $left) {
                $e['participants'] = $left > 0
                    ? 'На эту дату осталось мест: ' . $left
                    : 'На эту дату мест нет, выберите другую';
            }
        }
        return $e;
    }

    public static function review(array $d): array
    {
        $e = [];
        $rating = (int)($d['rating'] ?? 0);
        if ($rating < 1 || $rating > 5) {
            $e['rating'] = 'Поставьте оценку от 1 до 5';
        }
        $len = text_length(trim($d['text'] ?? ''));
        if ($len < 5) {
            $e['text'] = 'Напишите хотя бы пару слов';
        } elseif ($len > 500) {
            $e['text'] = 'Не более 500 символов';
        }
        return $e;
    }
}
