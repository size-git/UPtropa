<?php
class User
{
    public static function findByLogin(string $login): ?array
    {
        return Database::one('SELECT * FROM users WHERE login = ?', [$login]);
    }

    // Логин занят (без учёта регистра) или совпадает с логином администратора.
    public static function loginTaken(string $login): bool
    {
        if (strcasecmp($login, config('admin_login')) === 0) {
            return true;
        }
        return (bool)Database::value('SELECT 1 FROM users WHERE lower(login) = lower(?)', [$login]);
    }

    public static function create(array $d): int
    {
        Database::query(
            'INSERT INTO users (login, password_hash, full_name, phone, email) VALUES (?, ?, ?, ?, ?)',
            [
                trim($d['login']),
                password_hash($d['password'], PASSWORD_DEFAULT),
                trim($d['full_name']),
                trim($d['phone']),
                trim($d['email']),
            ]
        );
        return (int)Database::pdo()->lastInsertId();
    }
}
