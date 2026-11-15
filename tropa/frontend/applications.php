<?php
require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireUser();

$apps = Application::forUser(Auth::id());
$pageTitle = 'Мои бронирования';
require __DIR__ . '/partials/header.php';
?>
<main class="container">
    <div class="page-head">
        <h1>Мои бронирования</h1>
        <a class="btn" href="<?= url('frontend/application_create.php') ?>">Забронировать</a>
    </div>

    <?php if (!$apps): ?>
        <div class="card empty">
            <p>Пока нет ни одного бронирования. Выберите экскурсию и дату — это займёт минуту.</p>
        </div>
    <?php endif; ?>

    <div class="grid">
        <?php foreach ($apps as $i => $a): ?>
            <article class="card app fade-in" style="--i:<?= (int)$i ?>">
                <div class="app-head">
                    <h2><?= e($a['excursion_name']) ?></h2>
                    <span class="badge s<?= (int)$a['status_id'] ?>"><?= e($a['status_name']) ?></span>
                </div>
                <dl class="facts">
                    <div><dt>Дата</dt><dd><?= e(format_date($a['tour_date'])) ?></dd></div>
                    <div><dt>Участники</dt><dd><?= (int)$a['participants'] ?></dd></div>
                    <div><dt>Сумма</dt><dd><?= e(format_money((int)$a['total_price'])) ?></dd></div>
                    <div><dt>Оплата</dt><dd><?= e($a['payment_name']) ?></dd></div>
                </dl>

                <?php if ($a['review_id']): ?>
                    <div class="review-done">
                        <div class="stars" aria-label="Оценка <?= (int)$a['review_rating'] ?> из 5"><?= str_repeat('★', (int)$a['review_rating']) . str_repeat('☆', 5 - (int)$a['review_rating']) ?></div>
                        <p><?= e($a['review_text']) ?></p>
                    </div>
                <?php elseif ((int)$a['status_id'] === Application::DONE): ?>
                    <details class="review">
                        <summary>Оставить отзыв</summary>
                        <form method="post" action="<?= url('backend/actions/review_create.php') ?>" data-validate novalidate>
                            <input type="hidden" name="application_id" value="<?= (int)$a['id'] ?>">

                            <label>Оценка</label>
                            <select name="rating" required data-msg="Поставьте оценку от 1 до 5">
                                <option value="">Выберите</option>
                                <?php for ($r = 5; $r >= 1; $r--): ?>
                                    <option value="<?= $r ?>"><?= $r ?> из 5</option>
                                <?php endfor; ?>
                            </select>
                            <div class="field-error" data-for="rating"></div>

                            <label>Отзыв</label>
                            <textarea name="text" rows="3" maxlength="500" required minlength="5" data-msg="Напишите хотя бы пару слов"></textarea>
                            <div class="field-error" data-for="text"></div>

                            <button class="btn" type="submit">Отправить отзыв</button>
                        </form>
                    </details>
                <?php else: ?>
                    <p class="hint">Отзыв можно оставить после того, как экскурсия состоится.</p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
