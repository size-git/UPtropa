<?php
class Excursion
{
    public static function all(): array
    {
        return Database::all('SELECT * FROM excursions ORDER BY id');
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM excursions WHERE id = ?', [$id]);
    }
}
