<?php

return [
    // Вимикаємо стандартний <x-icon>, щоб звільнити тег для власної обгортки <x-ico>.
    'components' => [
        'disabled' => true,
        'default' => 'icon',
    ],
];
