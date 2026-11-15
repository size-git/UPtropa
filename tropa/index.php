<?php
require_once __DIR__ . '/backend/bootstrap.php';

$pageTitle = 'Экскурсии выходного дня';
$excursions = Excursion::all();
$slides = [
    ['slide1.jpg', 'Город с высоты привычного шага'],
    ['slide2.jpg', 'Музеи, в которые хочется вернуться'],
    ['slide3.jpg', 'Тропа за городом, чай на привале'],
    ['slide4.jpg', 'Группа, которая идёт в одну сторону'],
];

require __DIR__ . '/frontend/partials/header.php';
?>
<section class="slider" data-slider aria-label="Фотографии экскурсий">
    <div class="slides">
        <?php foreach ($slides as [$file, $caption]): ?>
            <figure class="slide">
                <img src="<?= url('frontend/assets/img/' . $file) ?>" alt="<?= e($caption) ?>" width="1200" height="800">
                <figcaption><?= e($caption) ?></figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
    <button class="slider-btn prev" type="button" data-prev aria-label="Назад">&#8249;</button>
    <button class="slider-btn next" type="button" data-next aria-label="Вперёд">&#8250;</button>
    <div class="dots">
        <?php foreach ($slides as $i => $s): ?>
            <span class="dot<?= $i === 0 ? ' active' : '' ?>"></span>
        <?php endforeach; ?>
    </div>
</section>

<main class="container">
    <section class="intro">
        <h1>Выходные — на маршруте</h1>
        <p>Три экскурсии по субботам и воскресеньям. Выберите маршрут, дату и количество человек — остальное мы подтвердим сами.</p>
    </section>

    <section class="grid">
        <?php foreach ($excursions as $ex): ?>
            <article class="card excursion">
                <h2><?= e($ex['name']) ?></h2>
                <p><?= e($ex['description']) ?></p>
                <div class="excursion-foot">
                    <div class="price"><?= e(format_money((int)$ex['price'])) ?> <small>за человека</small></div>
                    <a class="btn" href="<?= url('frontend/application_create.php?excursion=' . (int)$ex['id']) ?>">Забронировать</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?php require __DIR__ . '/frontend/partials/footer.php'; ?>
