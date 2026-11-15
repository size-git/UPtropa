<?php
// Параметры приложения. База — файл SQLite, создаётся сама при первом запуске.
return [
    'db_path'        => __DIR__ . '/database/tropa.sqlite',
    'admin_login'    => 'Admin',
    'admin_password' => 'Tropa2026',
    'per_page'       => 10,   // заявок на странице в админке
];
