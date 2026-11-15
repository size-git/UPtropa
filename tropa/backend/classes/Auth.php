<?php
// Вход, выход и проверка ролей. Администратор берётся из config.php, в БД его нет.
class Auth
{
    public static function attempt(string $login, string $password): bool
    {
        if ($login === config('admin_login')) {
            if (!hash_equals(config('admin_password'), $password)) {
                return false;
            }
            session_regenerate_id(true);
            unset($_SESSION['user_id'], $_SESSION['user_name']);
            $_SESSION['admin'] = true;
            return true;
        }

        $user = User::findByLogin($login);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        session_regenerate_id(true);
        unset($_SESSION['admin']);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['admin']);
        session_regenerate_id(true);
    }

    public static function isAdmin(): bool
    {
        return !empty($_SESSION['admin']);
    }

    public static function isUser(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): int
    {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    public static function name(): string
    {
        return $_SESSION['user_name'] ?? '';
    }

    // Страницы и обработчики для обычного пользователя.
    public static function requireUser(): void
    {
        if (self::isAdmin()) {
            redirect('frontend/admin.php');
        }
        if (!self::isUser()) {
            flash('error', 'Войдите, чтобы продолжить');
            redirect('frontend/login.php');
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            flash('error', 'Раздел только для администратора');
            redirect(self::isUser() ? 'frontend/applications.php' : 'frontend/login.php');
        }
    }
}
