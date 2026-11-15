<?php
class Application
{
    const NEW_ = 1, CONFIRMED = 2, DONE = 3, CANCELLED = 4;

    // Разрешённые переходы статусов: «Новая» → «Подтверждена» → «Экскурсия состоялась» или «Отменена».
    const FLOW = [
        self::NEW_      => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::DONE, self::CANCELLED],
    ];

    private const SELECT = 'SELECT a.*, e.name AS excursion_name, s.name AS status_name, pm.name AS payment_name
        FROM applications a
        JOIN excursions e ON e.id = a.excursion_id
        JOIN statuses s ON s.id = a.status_id
        JOIN payment_methods pm ON pm.id = a.payment_method_id';

    public static function statuses(): array
    {
        return Database::all('SELECT * FROM statuses ORDER BY id');
    }

    public static function paymentMethods(): array
    {
        return Database::all('SELECT * FROM payment_methods ORDER BY id');
    }

    // Сколько мест ещё свободно на экскурсию в эту дату (отменённые заявки не считаются).
    public static function seatsLeft(int $excursionId, string $isoDate): int
    {
        $limit = (int)Database::value('SELECT seats_limit FROM excursions WHERE id = ?', [$excursionId]);
        $taken = (int)Database::value(
            'SELECT COALESCE(SUM(participants), 0) FROM applications WHERE excursion_id = ? AND tour_date = ? AND status_id != ?',
            [$excursionId, $isoDate, self::CANCELLED]
        );
        return max(0, $limit - $taken);
    }

    // $d уже прошёл Validator. Стоимость считаем здесь, а не берём из формы.
    public static function create(int $userId, array $d): int
    {
        $excursion = Excursion::find((int)$d['excursion_id']);
        $iso   = DateTime::createFromFormat('d.m.Y', $d['tour_date'])->format('Y-m-d');
        $count = (int)$d['participants'];

        Database::query(
            'INSERT INTO applications (user_id, excursion_id, tour_date, participants, payment_method_id, status_id, total_price)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$userId, $excursion['id'], $iso, $count, (int)$d['payment_method_id'], self::NEW_, $excursion['price'] * $count]
        );
        return (int)Database::pdo()->lastInsertId();
    }

    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT x.*, r.id AS review_id, r.rating AS review_rating, r.text AS review_text
             FROM (' . self::SELECT . ' WHERE a.user_id = ?) x
             LEFT JOIN reviews r ON r.application_id = x.id
             ORDER BY x.id DESC',
            [$userId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE a.id = ?', [$id]);
    }

    // Все заявки для админки: фильтры + пагинация.
    public static function search(?int $statusId, ?int $excursionId, int $page, int $perPage): array
    {
        $where = [];
        $params = [];
        if ($statusId) {
            $where[] = 'a.status_id = ?';
            $params[] = $statusId;
        }
        if ($excursionId) {
            $where[] = 'a.excursion_id = ?';
            $params[] = $excursionId;
        }
        $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $total = (int)Database::value('SELECT COUNT(*) FROM applications a' . $w, $params);
        $pages = max(1, (int)ceil($total / $perPage));
        $page  = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $rows = Database::all(
            'SELECT a.*, u.full_name, u.phone, e.name AS excursion_name, s.name AS status_name, pm.name AS payment_name
             FROM applications a
             JOIN users u ON u.id = a.user_id
             JOIN excursions e ON e.id = a.excursion_id
             JOIN statuses s ON s.id = a.status_id
             JOIN payment_methods pm ON pm.id = a.payment_method_id' . $w .
            ' ORDER BY a.id DESC LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset,
            $params
        );
        return ['rows' => $rows, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    // Какие статусы можно выбрать из текущего: [id => название].
    public static function nextStatuses(int $statusId): array
    {
        $result = [];
        foreach (self::statuses() as $s) {
            if (in_array((int)$s['id'], self::FLOW[$statusId] ?? [], true)) {
                $result[(int)$s['id']] = $s['name'];
            }
        }
        return $result;
    }

    public static function setStatus(int $id, int $newStatus): bool
    {
        $app = self::find($id);
        if (!$app || !in_array($newStatus, self::FLOW[(int)$app['status_id']] ?? [], true)) {
            return false;
        }
        Database::query('UPDATE applications SET status_id = ? WHERE id = ?', [$newStatus, $id]);
        return true;
    }
}
