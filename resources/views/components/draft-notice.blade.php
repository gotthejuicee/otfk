{{-- Плашка режиму чернетки/превʼю: контент видно лише залогіненим адміністраторам --}}
@props(['message' => 'Чернетка — не опубліковано на сайті. Цю сторінку бачите лише ви як адміністратор.'])

<div class="border-b border-amber-200 bg-amber-50">
    <div class="container-site flex items-center gap-2 py-2.5 text-sm font-medium text-amber-800">
        <x-ico name="eye-slash" class="h-4 w-4 shrink-0" />
        <span>{{ $message }}</span>
    </div>
</div>
