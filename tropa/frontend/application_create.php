<?php
require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireUser();

$errors = pull('errors') ?? [];
$old = pull('old') ?? [];
$excursions = Excursion::all();
$payments = Application::paymentMethods();
$selected = (int)($old['excursion_id'] ?? $_GET['excursion'] ?? 0);

$pageTitle = 'Бронирование';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
    <form class="card form" method="post" action="<?= url('backend/actions/application_create.php') ?>" data-validate data-price-form novalidate>
        <h1>Бронирование</h1>

        <label for="excursion_id">Экскурсия</label>
        <select id="excursion_id" name="excursion_id" required data-msg="Выберите экскурсию"
                class="<?= isset($errors['excursion_id']) ? 'invalid' : '' ?>">
            <option value="">Выберите из списка</option>
            <?php foreach ($excursions as $ex): ?>
                <option value="<?= (int)$ex['id'] ?>" data-price="<?= (int)$ex['price'] ?>" <?= $selected === (int)$ex['id'] ? 'selected' : '' ?>>
                    <?= e($ex['name']) ?> — <?= e(format_money((int)$ex['price'])) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="field-error" data-for="excursion_id"><?= e($errors['excursion_id'] ?? '') ?></div>

        <label for="tour_date">Дата (суббота или воскресенье)</label>
        <input id="tour_date" name="tour_date" type="text" inputmode="numeric" placeholder="ДД.ММ.ГГГГ"
               value="<?= e($old['tour_date'] ?? '') ?>" required pattern="\d{2}\.\d{2}\.\d{4}"
               data-weekend="1" data-msg="Формат даты: ДД.ММ.ГГГГ"
               class="<?= isset($errors['tour_date']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="tour_date"><?= e($errors['tour_date'] ?? '') ?></div>

        <label for="participants">Участников</label>
        <input id="participants" name="participants" type="number" min="1" max="10" step="1"
               value="<?= e($old['participants'] ?? '1') ?>" required data-msg="Участников — от 1 до 10"
               class="<?= isset($errors['participants']) ? 'invalid' : '' ?>">
        <div class="field-error" data-for="participants"><?= e($errors['participants'] ?? '') ?></div>

        <label for="payment_method_id">Способ оплаты</label>
        <select id="payment_method_id" name="payment_method_id" required data-msg="Выберите способ оплаты"
                class="<?= isset($errors['payment_method_id']) ? 'invalid' : '' ?>">
            <option value="">Выберите из списка</option>
            <?php foreach ($payments as $p): ?>
                <option value="<?= (int)$p['id'] ?>" <?= (int)($old['payment_method_id'] ?? 0) === (int)$p['id'] ? 'selected' : '' ?>>
                    <?= e($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="field-error" data-for="payment_method_id"><?= e($errors['payment_method_id'] ?? '') ?></div>

        <div class="total">Итого: <strong data-total>0 ₽</strong></div>

        <button class="btn block" type="submit">Отправить</button>
    </form>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
