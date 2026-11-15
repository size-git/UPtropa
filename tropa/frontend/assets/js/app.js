document.addEventListener('DOMContentLoaded', function () {

    /* ---------- меню на телефоне ---------- */
    var menuBtn = document.querySelector('[data-menu-btn]');
    var menu = document.querySelector('[data-menu]');
    if (menuBtn && menu) {
        menuBtn.addEventListener('click', function () {
            var open = menu.classList.toggle('open');
            menuBtn.setAttribute('aria-expanded', open);
        });
    }

    /* ---------- всплывающие уведомления ---------- */
    document.querySelectorAll('.toast').forEach(function (t) {
        setTimeout(function () { t.classList.add('hide'); }, 4000);
        t.addEventListener('click', function () { t.classList.add('hide'); });
    });

    /* ---------- слайдер: смена каждые 3 секунды ---------- */
    var slider = document.querySelector('[data-slider]');
    if (slider) {
        var track = slider.querySelector('.slides');
        var dots = slider.querySelectorAll('.dot');
        var count = track.children.length;
        var index = 0;
        var timer;

        var go = function (i) {
            index = (i + count) % count;
            track.style.transform = 'translateX(-' + index * 100 + '%)';
            dots.forEach(function (d, j) { d.classList.toggle('active', j === index); });
        };
        var start = function () { timer = setInterval(function () { go(index + 1); }, 3000); };
        var restart = function () { clearInterval(timer); start(); };

        slider.querySelector('[data-prev]').addEventListener('click', function () { go(index - 1); restart(); });
        slider.querySelector('[data-next]').addEventListener('click', function () { go(index + 1); restart(); });
        start();
    }

    /* ---------- проверка форм на клиенте (сервер всё равно проверит заново) ---------- */
    var checkWeekend = function (field) {
        field.setCustomValidity('');
        var m = /^(\d{2})\.(\d{2})\.(\d{4})$/.exec(field.value.trim());
        if (!m) return;
        var d = new Date(+m[3], +m[2] - 1, +m[1]);
        if (d.getMonth() !== +m[2] - 1) {
            field.setCustomValidity('Такой даты не существует');
        } else if (d < new Date(new Date().setHours(0, 0, 0, 0))) {
            field.setCustomValidity('Дата не может быть в прошлом');
        } else if (d.getDay() !== 0 && d.getDay() !== 6) {
            field.setCustomValidity('Экскурсии проходят только в субботу и воскресенье');
        }
    };

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        var show = function (field) {
            var box = form.querySelector('[data-for="' + field.name + '"]');
            if (!box) return;
            var ok = field.validity.valid;
            box.textContent = ok ? '' : (field.validity.customError ? field.validationMessage : (field.dataset.msg || field.validationMessage));
            field.classList.remove('invalid');
            if (!ok) {
                void field.offsetWidth;          // перезапуск анимации ошибки
                field.classList.add('invalid');
            }
        };

        form.addEventListener('submit', function (e) {
            var bad = null;
            form.querySelectorAll('input, select, textarea').forEach(function (f) {
                if (f.dataset.weekend) checkWeekend(f);
                show(f);
                if (!f.validity.valid && !bad) bad = f;
            });
            if (bad) {
                e.preventDefault();
                bad.focus();
            }
        });

        form.addEventListener('input', function (e) {
            if (e.target.dataset.weekend) checkWeekend(e.target);
            if (e.target.classList.contains('invalid')) show(e.target);
        });
    });

    /* ---------- сумма заявки на лету (цена × участники) ---------- */
    var priceForm = document.querySelector('[data-price-form]');
    if (priceForm) {
        var select = priceForm.querySelector('[name="excursion_id"]');
        var people = priceForm.querySelector('[name="participants"]');
        var out = priceForm.querySelector('[data-total]');
        var recalc = function () {
            var opt = select.options[select.selectedIndex];
            var price = opt ? +opt.dataset.price || 0 : 0;
            var n = Math.min(10, Math.max(0, parseInt(people.value, 10) || 0));
            out.textContent = (price * n).toLocaleString('ru-RU') + ' ₽';
            out.classList.remove('bump');
            void out.offsetWidth;
            out.classList.add('bump');
        };
        select.addEventListener('change', recalc);
        people.addEventListener('input', recalc);
        recalc();
    }
});
