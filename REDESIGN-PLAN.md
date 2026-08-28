# План редизайна всех экранов otfk

Рабочий файл (не коммитить). Конвейер на каждый пункт: скрин с https://just-test.shop → ChatGPT-макет → имплементация «по мотивам» → тесты/сборка → деплой в `feature/**` (детали и грабли — в памяти `chatgpt-design-pipeline`).

Статусы: `[x]` готово · `[ ]` в очереди · `[~]` в работе.

## 0. Глобальный каркас (проверяется на каждой странице, но правится один раз)

- [x] **Главная** — `/` — `home.blade.php` + `components/home/hero-slider.blade.php` (коммит `f672178`)
- [x] **Хедер: топбар, меню, мобильное меню, поиск** — `components/layouts/app.blade.php` (меню DB-driven, 12 пунктов — проверять на реальных данных)
- [x] **Футер** — там же, `components/layouts/app.blade.php`
- [x] **Хлебные крошки** — `components/breadcrumbs.blade.php` (ChatGPT-макет новостей сохранил текущий вид: Головна › Новини в hero — оставлено как есть)
- [ ] **Общие компоненты**: `page-hero`, `news-card`, `staff-card`, `empty-state`, `lead-excerpt`, `heritage-frame`, `prose/article` — обновлять по ходу первых внутренних страниц, фиксировать единый стиль

## 1. Новости и медиа

- [x] **Список новостей** — `/novyny` — `news/index.blade.php` (featured-карточка на 1-й странице без фильтров; бейдж категории на фото; подзаголовок в hero; украинская пагинация `vendor/pagination/tailwind.blade.php` — общая, задействована и на `/video`)
- [ ] **Новость (детальная)** — `/novyny/{slug}` — `news/show.blade.php` (лайк-кнопка, шаринг; помнить про `NewsObserver`/Telegram — контент не трогать)
- [ ] **Видео** — `/video` — `videos/index.blade.php`
- [ ] **Галереи (список)** — `/halereya` — `galleries/index.blade.php`
- [ ] **Галерея (детальная, лайтбокс)** — `/halereya/{slug}` — `galleries/show.blade.php`

## 2. Абитуриенту / интерактив

- [ ] **Заявка абитуриента (форма)** — `/zayavka` — `applicants/create.blade.php` (+ состояния ошибок валидации и успеха)
- [ ] **Специальности (список)** — `/spetsialnosti` — `specialties/index.blade.php`
- [ ] **Специальность (детальная)** — `/spetsialnosti/{slug}` — `specialties/show.blade.php`
- [ ] **FAQ** — `/faq` — `faq/index.blade.php` (аккордеон)
- [ ] **Квиз** — `/kviz` — `quiz/index.blade.php` (все шаги: вопросы, прогресс, результат)
- [ ] **События** — `/podiyi` — `events/index.blade.php` (+ кнопка ICS)
- [ ] **Расписание звонков** — `/rozklad-dzvinkiv` — `bells/index.blade.php` (активный урок подсвечивается — проверить оба состояния)

## 3. О колледже / структура

- [ ] **Структура (список подразделений)** — `/struktura` — `structure/index.blade.php`
- [ ] **Подразделение (детальное)** — `/struktura/{slug}` — `structure/show.blade.php` (персонал внутри)
- [ ] **Администрация** — `/administratsiya` — `staff/administration.blade.php`
- [ ] **Документы (категории)** — `/dokumenty` — `documents/index.blade.php`
- [ ] **Документы категории** — `/dokumenty/{slug}` — `documents/category.blade.php` (список файлов, много длинных названий)

## 4. Динамические страницы (один шаблон `pages/show.blade.php`, 3 варианта отображения)

Шаблон один, но прогнать конвейер на каждом варианте — вёрстка разная:

- [ ] **Хаб с дочерними страницами** (карточки-ссылки) — пример: `/abituriyentu` (7 хардкод-ссылок — слаг не менять!), `/studentu`
- [ ] **Обычная контентная страница** (prose) — пример: `/pro-koledzh`, `/biblioteka` (проверить длинные импортированные страницы со старого сайта: таблицы, списки файлов)
- [ ] **Heritage-страница с drop cap** — `/istoriya` (слаг не менять, `heritage-frame` + drop cap закреплены в шаблоне)

## 5. Сервисные экраны

- [ ] **Поиск (результаты + пустой запрос + ничего не найдено + подсказки-дропдаун)** — `/poshuk` — `search/index.blade.php`
- [ ] **Контакты (карта, форма обратной связи + состояния ошибок/успеха)** — `/kontakty` — `contacts.blade.php`
- [ ] **404** — `errors/404.blade.php` (открыть любой несуществующий URL)
- [ ] **403 / 500 / 503** — `errors/{403,500,503}.blade.php` (одним заходом, стиль от 404)

## 6. Вне охвата дизайна (не экраны — пропускаем)

- RSS `/novyny/feed.xml`, `/sitemap.xml`, `/robots.txt`, ICS событий — машинные форматы.
- Email-шаблоны `emails/applicant.blade.php`, `emails/feedback.blade.php` — при желании отдельной задачей в конце (инлайн-стили, не Tailwind).
- Filament-админка (`filament/**`) — стандартная тема, редизайн не планируется.

## Чек на каждую задачу

1. Прочитать Feature-тесты на контракты шаблона (grep по классам/aria в `tests/Feature/`).
2. Скрин текущей страницы (десктоп 1920 + мобильный) → ChatGPT-макет.
3. Имплементация: только DB-driven контент, safelist для динамических классов, light-only.
4. `npm run build` → `composer test` (эталон — 107 passed) → визуальная проверка 1920px и mobile.
5. Отметить пункт здесь; новые PoC-элементы — в таблицу ARCHITECTURE.md.
