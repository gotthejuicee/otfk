<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Сидити БД при першій міграції тестового процесу (RefreshDatabase виконує
     * migrate:fresh --seed лише раз за процес — той клас, що бутиться першим,
     * визначає наявність сиду; тому вмикаємо глобально, щоб тести не залежали
     * від порядку запуску).
     */
    protected bool $seed = true;
}
