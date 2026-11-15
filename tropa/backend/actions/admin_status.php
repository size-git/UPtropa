<?php
require_once __DIR__ . '/../bootstrap.php';
Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('frontend/admin.php');
}

$id     = (int)($_POST['id'] ?? 0);
$status = (int)($_POST['status_id'] ?? 0);

if (Application::setStatus($id, $status)) {
    $_SESSION['changed_id'] = $id;   // чтобы подсветить строку после возврата
    flash('success', 'Заявка №' . $id . ': статус изменён');
} else {
    flash('error', 'Такой переход статуса недоступен');
}

// Возвращаемся на ту же страницу с теми же фильтрами.
parse_str((string)($_POST['back'] ?? ''), $back);
$query = http_build_query(array_intersect_key($back, array_flip(['status', 'excursion', 'page'])));
redirect('frontend/admin.php' . ($query ? '?' . $query : ''));
