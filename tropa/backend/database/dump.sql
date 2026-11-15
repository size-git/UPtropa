-- «Тропа»: структура БД (SQLite) + справочники.
-- Файл применяется автоматически, если tropa.sqlite ещё не создан.
PRAGMA foreign_keys = ON;

CREATE TABLE users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    login         TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    full_name     TEXT NOT NULL,
    phone         TEXT NOT NULL,
    email         TEXT NOT NULL,
    created_at    TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE statuses (
    id   INTEGER PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE payment_methods (
    id   INTEGER PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE excursions (
    id          INTEGER PRIMARY KEY,
    name        TEXT NOT NULL UNIQUE,
    description TEXT NOT NULL DEFAULT '',
    price       INTEGER NOT NULL CHECK (price > 0),       -- цена за человека, ₽
    seats_limit INTEGER NOT NULL DEFAULT 20 CHECK (seats_limit > 0)  -- мест на одну дату
);

CREATE TABLE applications (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id           INTEGER NOT NULL REFERENCES users(id),
    excursion_id      INTEGER NOT NULL REFERENCES excursions(id),
    tour_date         TEXT    NOT NULL,                   -- YYYY-MM-DD
    participants      INTEGER NOT NULL CHECK (participants BETWEEN 1 AND 10),
    payment_method_id INTEGER NOT NULL REFERENCES payment_methods(id),
    status_id         INTEGER NOT NULL DEFAULT 1 REFERENCES statuses(id),
    total_price       INTEGER NOT NULL,                   -- цена × участники, считает сервер
    created_at        TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    application_id INTEGER NOT NULL UNIQUE REFERENCES applications(id),
    user_id        INTEGER NOT NULL REFERENCES users(id),
    rating         INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
    text           TEXT NOT NULL,
    created_at     TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO statuses (id, name) VALUES
    (1, 'Новая'), (2, 'Подтверждена'), (3, 'Экскурсия состоялась'), (4, 'Отменена');

INSERT INTO payment_methods (id, name) VALUES
    (1, 'Наличными'), (2, 'Переводом по номеру телефона');

INSERT INTO excursions (id, name, description, price, seats_limit) VALUES
    (1, 'Обзорная по городу',      'Главные площади, набережная и дворы, которых нет в путеводителях. Пешком, около трёх часов.', 1500, 20),
    (2, 'Музейный квартал',        'Три музея в одном маршруте и разговор с экскурсоводом в каждом зале. Билеты уже включены.',  1200, 20),
    (3, 'Загородный эко-маршрут',  'Лесная тропа, река и привал с чаем. Берите удобную обувь, остальное выдадим на месте.',      2500, 20);
