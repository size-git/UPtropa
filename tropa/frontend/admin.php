<?php
require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireAdmin();

$statusId    = (int)($_GET['status'] ?? 0) ?: null;
$excursionId = (int)($_GET['excursion'] ?? 0) ?: null;
$result = Application::search($statusId, $excursionId, (int)($_GET['page'] ?? 1), (int)config('per_page'));

$statuses = Application::statuses();
$excursions = Excursion::all();
$changedId = (int)pull('changed_id');

// Параметры, которые нужно сохранять в ссылках пагинации и после смены статуса.
$filters = array_filter(['status' => $statusId, 'excursion' => $excursionId]);
$back = http_build_query($filters + ['page' => $result['page']]);

$pageTitle = 'Панель администратора';
require __DIR__ . '/partials/header.php';
?>
<main class="container wide">
    <div class="page-head">
        <h1>Заявки</h1>
        <span class="muted">Всего: <?= (int)$result['total'] ?></span>
    </div>

    <form class="filters" method="get">
        <select name="status" aria-label="Статус">
            <option value="">Все статусы</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= (int)$s['id'] ?>" <?= $statusId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="excursion" aria-label="Экскурсия">
            <option value="">Все экскурсии</option>
            <?php foreach ($excursions as $ex): ?>
                <option value="<?= (int)$ex['id'] ?>" <?= $excursionId === (int)$ex['id'] ? 'selected' : '' ?>><?= e($ex['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Показать</button>
        <a class="btn ghost" href="<?= url('frontend/admin.php') ?>">Сбросить</a>
    </form>

    <?php if (!$result['rows']): ?>
        <div class="card empty"><p>По этим фильтрам заявок нет.</p></div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr><th>№</th><th>Пользователь</th><th>Экскурсия</th><th>Дата</th><th>Чел.</th><th>Сумма</th><th>Оплата</th><th>Статус</th><th>Действие</th></tr>
            </thead>
            <tbody>
            <?php foreach ($result['rows'] as $a): ?>
                <tr class="<?= (int)$a['id'] === $changedId ? 'changed' : '' ?>">
                    <td data-label="№"><?= (int)$a['id'] ?></td>
                    <td data-label="Пользователь"><?= e($a['full_name']) ?><br><small><?= e($a['phone']) ?></small></td>
                    <td data-label="Экскурсия"><?= e($a['excursion_name']) ?></td>
                    <td data-label="Дата"><?= e(format_date($a['tour_date'])) ?></td>
                    <td data-label="Чел."><?= (int)$a['participants'] ?></td>
                    <td data-label="Сумма"><?= e(format_money((int)$a['total_price'])) ?></td>
                    <td data-label="Оплата"><?= e($a['payment_name']) ?></td>
                    <td data-label="Статус"><span class="badge s<?= (int)$a['status_id'] ?>"><?= e($a['status_name']) ?></span></td>
                    <td data-label="Действие">
                        <?php $next = Application::nextStatuses((int)$a['status_id']); ?>
                        <?php if (!$next): ?>
                            <span class="muted">—</span>
                        <?php else: ?>
                            <form class="actions" method="post" action="<?= url('backend/actions/admin_status.php') ?>">
                                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                                <input type="hidden" name="back" value="<?= e($back) ?>">
                                <?php foreach ($next as $nid => $nname): ?>
                                    <button class="btn small <?= $nid === Application::CANCELLED ? 'danger' : '' ?>" type="submit" name="status_id" value="<?= $nid ?>"><?= e($nname) ?></button>
                                <?php endforeach; ?>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($result['pages'] > 1): ?>
            <nav class="pager" aria-label="Страницы">
                <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
                    <a class="<?= $p === $result['page'] ? 'current' : '' ?>" href="?<?= e(http_build_query($filters + ['page' => $p])) ?>"><?= $p ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
