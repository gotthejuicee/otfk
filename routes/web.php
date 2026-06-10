<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/novyny', [NewsController::class, 'index'])->name('news.index');
Route::get('/novyny/{news:slug}', [NewsController::class, 'show'])->name('news.show');
Route::post('/novyny/{news:slug}/vpodobayka', [NewsController::class, 'like'])
    ->middleware('throttle:30,1')->name('news.like');

Route::get('/video', [VideoController::class, 'index'])->name('video.index');

Route::get('/rozklad-dzvinkiv', [App\Http\Controllers\BellScheduleController::class, 'index'])->name('bells');
Route::get('/podiyi', [App\Http\Controllers\EventController::class, 'index'])->name('events');
Route::get('/podiyi/{event}/ics', [App\Http\Controllers\EventController::class, 'ics'])->name('events.ics');

Route::get('/faq', [App\Http\Controllers\FaqController::class, 'index'])->name('faq');
Route::get('/kviz', [App\Http\Controllers\QuizController::class, 'index'])->name('quiz');

// Заявка абітурієнта
Route::get('/zayavka', [App\Http\Controllers\ApplicantRequestController::class, 'create'])->name('applicants.create');
Route::post('/zayavka', [App\Http\Controllers\ApplicantRequestController::class, 'store'])
    ->middleware('throttle:5,1')->name('applicants.store');

// Публічна інформація (документи)
Route::get('/dokumenty', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/dokumenty/{documentCategory:slug}', [DocumentController::class, 'category'])->name('documents.category');

// Спеціальності
Route::get('/spetsialnosti', [SpecialtyController::class, 'index'])->name('specialties.index');
Route::get('/spetsialnosti/{specialty:slug}', [SpecialtyController::class, 'show'])->name('specialties.show');

// Структура та персонал
Route::get('/struktura', [StructureController::class, 'index'])->name('structure.index');
Route::get('/struktura/{department:slug}', [StructureController::class, 'show'])->name('structure.show');
Route::get('/administratsiya', [StaffController::class, 'administration'])->name('staff.administration');

// Галерея
Route::get('/halereya', [GalleryController::class, 'index'])->name('galleries.index');
Route::get('/halereya/{gallery:slug}', [GalleryController::class, 'show'])->name('galleries.show');

// Пошук
Route::get('/poshuk', [SearchController::class, 'index'])->name('search');
Route::get('/poshuk/pidkazky', [SearchController::class, 'suggest'])
    ->middleware('throttle:60,1')->name('search.suggest');

Route::get('/kontakty', [ContactController::class, 'index'])->name('contacts');
Route::post('/kontakty', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contacts.store');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
    $body = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /livewire\n\nSitemap: " . url('/sitemap.xml') . "\n";

    return response($body, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->name('robots');

/*
 | Динамічна редагована сторінка (catch-all). Реєструється ОСТАННЬОЮ та
 | виключає службові префікси, щоб не перехоплювати /admin, /livewire тощо.
 */
Route::get('/{page:slug}', [PageController::class, 'show'])
    ->where('page', '^(?!(?:admin|livewire|novyny|video|kontakty|dokumenty|spetsialnosti|struktura|administratsiya|halereya|poshuk|sitemap|storage|up|build|vendor)$).+$')
    ->name('pages.show');
