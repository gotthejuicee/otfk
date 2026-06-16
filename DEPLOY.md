# Деплой ОТФК на Хостинг Україна (ukraine.com.ua)

Тариф «Кращий»: есть **SSH**, **Composer 2** (уже установлен), выбор **версии PHP** — значит деплой идёт по стандартному пути.

> ✅ Перед прод-деплоем уже сделано в коде: `User::canAccessPanel()` (иначе Filament отдаёт 403 на проде) и шаблон `.env.production.example`.

---

## 0. Подготовка локально (один раз)

```bash
# 1) собрать фронтенд (создаёт public/build — на хостинге нет npm, поэтому собираем тут)
npm run build

# 2) узнать свой APP_KEY (понадобится для .env на сервере)
#    он уже есть в локальном .env, строка APP_KEY=base64:...
```

Версия PHP: проект на **Laravel 12 → нужен PHP 8.2+** (лучше 8.3).

---

## Вариант А — через Git + SSH (рекомендую: удобно обновлять)

На сервере нет `vendor/` и `public/build` (они в `.gitignore`), поэтому `vendor` ставим Composer'ом на сервере, а `public/build` заливаем отдельно.

### 1. Залить код
```bash
# выбрать в панели версию PHP 8.2/8.3 для сайта (Сайти → Налаштування → Версія PHP)
# подключиться по SSH, перейти в каталог сайта:
cd ~/ВАШ-ДОМЕН/www

# клонировать репозиторий (или загрузить файлы файловым менеджером)
git clone https://github.com/ВАШ_РЕПОЗИТОРИЙ.git .
```

### 2. Зависимости + .env
```bash
# Composer уже есть. Если php в PATH не та версия — указать явный путь, напр. /usr/local/php83/bin/php
composer install --no-dev --optimize-autoloader

cp .env.production.example .env
# отредактировать .env: APP_URL, APP_KEY (скопировать из локального), DB_*
nano .env
```

### 3. База данных
В панели хостинга: **MySQL → создать базу + пользователя**, дать пользователю права на базу. Подставить их в `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST=localhost`).

> ⚠️ Репозиторий публичный — в `.env` задайте свои `ADMIN_EMAIL` и `ADMIN_PASSWORD` **до** этой команды
> (иначе создастся админ с дефолтным паролем `password`, который виден в коде). Делайте seed **до** `php artisan optimize` (кэш конфига ломает чтение env в сидере).

```bash
# создаст все таблицы И наполнит сайт (меню, страницы, демо-контент) + админа из ADMIN_EMAIL/ADMIN_PASSWORD
php artisan migrate --seed --force
```

### 4. Ассеты, симлинк, кеш
```bash
php artisan filament:assets      # опубликовать CSS/JS админки в public (иначе админка без стилей)
php artisan storage:link         # симлинк public/storage → storage/app/public (для загруженных фото)
php artisan optimize             # кеш конфигов/роутов/вью (быстрее). При проблемах: php artisan optimize:clear
```
`public/build` собран локально и в `.gitignore` — залейте папку `public/build` на сервер через файловый менеджер/SFTP.

### 5. Корень сайта → public
В панели: **Сайти → Налаштування → Кореневий каталог** → указать `public`
(альтернатива — `.htaccess`-редирект на `public`, но смена корня в панели чище).

---

## Вариант Б — архивом (проще для первого раза, без Git)

1. **Локально:** `composer install --no-dev --optimize-autoloader` + `npm run build`.
2. Заархивировать проект в `.zip` **без** `node_modules`, `.git`, `.env` (но **с** `vendor/` и `public/build/`).
3. В файловом менеджере хостинга: загрузить zip в каталог сайта и распаковать.
4. Создать БД в панели; залить `.env` (из `.env.production.example`).
5. **База:** либо `php artisan migrate --seed --force` по SSH, либо экспортировать локальную базу
   `mysqldump -u root otfk > otfk.sql` и импортировать `otfk.sql` через **phpMyAdmin** в панели.
6. По SSH (один раз): `php artisan filament:assets && php artisan storage:link && php artisan optimize`.
7. Корень сайта → `public` (как в Варианте А, шаг 5).

---

## Чек-лист «не забыть»

- [ ] PHP **8.2+** выбран для сайта
- [ ] Корень сайта = **`public`**
- [ ] `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` заполнен, `APP_URL=https://домен`, `DB_*`
- [ ] `php artisan migrate --seed --force` (или импорт `otfk.sql`)
- [ ] `php artisan storage:link` (фото из админки)
- [ ] `php artisan filament:assets` (стили админки)
- [ ] `public/build` загружен (собирается локально)
- [ ] права на запись: `chmod -R 775 storage bootstrap/cache` (если будут ошибки 500)

## Проверить после деплоя

1. `https://домен/` — сайт, плитки, новости
2. `https://домен/admin` — вход `admin@otfk.od.ua` / `password` → **сразу сменить пароль** (Профіль в правом верхнем углу)
3. Загрузить логотип, баннер, пару новостей — проверить, что фото отображаются (нужен `storage:link`)

## Фоновые задачи (Telegram, письма)

Автопостинг новостей в Telegram и письма с форм (заявка/контакты) отправляются
**после** отдачи страницы — через `dispatch(...)->afterResponse()`. Это
терминирующий колбэк: Laravel выполняет его в том же процессе на этапе
`terminate()`, уже после ответа браузеру. Поэтому:

- **queue-воркер не нужен** (на шаред-хостинге его и нет) — `QUEUE_CONNECTION`
  для этих задач не задействуется, таблица `jobs` не накапливается;
- посетитель/админ не ждёт ни Telegram, ни SMTP;
- **не переводите эти задачи в `ShouldQueue`** без запущенного `queue:work` —
  тогда они улетят в очередь и без воркера не выполнятся никогда.

Если Telegram-пост не ушёл (API недоступен) — новость всё равно помечается
как опубликованная (защита от дублей), а ошибка пишется в лог (`Log::warning`).
Повторить вручную: очистить `telegram_posted_at` у новости и пересохранить её.

## Обновление сайта потом (Вариант А)

```bash
cd ~/ВАШ-ДОМЕН/www
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan optimize
# + залить свежий public/build, если менялся фронтенд
```
