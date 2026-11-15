<?php
class Review
{
    // Возвращает текст ошибки или null, если отзыв сохранён.
    // Права проверяются здесь, на сервере: чужая заявка, не тот статус, повторный отзыв.
    public static function create(int $userId, int $applicationId, int $rating, string $text): ?string
    {
        $app = Application::find($applicationId);
        if (!$app || (int)$app['user_id'] !== $userId) {
            return 'Заявка не найдена';
        }
        if ((int)$app['status_id'] !== Application::DONE) {
            return 'Отзыв можно оставить только после статуса «Экскурсия состоялась»';
        }
        if (Database::value('SELECT 1 FROM reviews WHERE application_id = ?', [$applicationId])) {
            return 'Вы уже оставили отзыв об этой экскурсии';
        }
        Database::query(
            'INSERT INTO reviews (application_id, user_id, rating, text) VALUES (?, ?, ?, ?)',
            [$applicationId, $userId, $rating, trim($text)]
        );
        return null;
    }
}
