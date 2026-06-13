# Сайт Одеського технічного фахового коледжу ОНТУ

[![Тести](https://github.com/gotthejuicee/otfk/actions/workflows/tests.yml/badge.svg)](https://github.com/gotthejuicee/otfk/actions/workflows/tests.yml)

Сучасний сайт коледжу: швидка публічна частина + повноцінна адмінпанель українською. Усе наповнення редагується без жодного рядка коду.

## Можливості

- **Новини** — вподобайки та перегляди без реєстрації, кнопки «поділитися» (Facebook / Telegram / Viber), автопостинг нових новин у Telegram-канал, лайтбокс для фото, фільтр за роками, блок «цього дня в коледжі» з архіву
- **Для вступників** — онлайн-заявка (лист приймальній комісії + CRM в адмінці), профорієнтаційний квіз «яка спеціальність підходить», FAQ з розміткою для Google
- **Події** — календар із кнопками «додати в Google Calendar / .ics»
- **Розклад дзвінків** — із живим індикатором «зараз пара» у шапці
- **Контент** — документи, спеціальності, структура й персонал, фотогалереї, відео, «Коледж у цифрах», відгуки
- **Зручності** — живий пошук із підказками, нічна підсвітка, банер термінових оголошень, прелоад сторінок, приватна аналітика відвідувань (без кук і сторонніх сервісів)
- **SEO** — sitemap, Open Graph, JSON-LD (NewsArticle, Event, Course, FAQPage), захисні HTTP-заголовки
- **Адмінка** — Filament: дашборд із графіками, швидкі дії, керування всім контентом і налаштуваннями

## Стек

Laravel 12 · Filament 3 · Tailwind CSS · Alpine.js · MySQL · PHP 8.3

## Запуск локально

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# вкажіть доступ до БД у .env, потім:
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Адмінка — за адресою `/admin` (логін задається у `.env`: `ADMIN_EMAIL` / `ADMIN_PASSWORD`).

## Тести

```bash
php artisan test
```

Тести виконуються на SQLite у пам’яті й автоматично проганяються в GitHub Actions на кожен push.

## Документація

Посібник адміністратора для нетехнічних користувачів — у файлі [`docs/posibnyk-administratora.html`](docs/posibnyk-administratora.html) (відкрити у браузері, за потреби зберегти як PDF: `Ctrl + P`).
