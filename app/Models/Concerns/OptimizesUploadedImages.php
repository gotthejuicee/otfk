<?php

namespace App\Models\Concerns;

use App\Support\ImageOptimizer;

trait OptimizesUploadedImages
{
    public static function bootOptimizesUploadedImages(): void
    {
        static::saved(function (self $model): void {
            foreach (static::optimizedImageFields() as $field) {
                if (! $model->wasChanged($field)) {
                    continue;
                }

                $old = $model->getOriginal($field);

                if (filled($old) && $old !== $model->{$field}) {
                    ImageOptimizer::deleteVariants($old);
                }

                if (filled($model->{$field})) {
                    ImageOptimizer::toWebp($model->{$field});
                }
            }
        });

        static::deleted(function (self $model): void {
            foreach (static::optimizedImageFields() as $field) {
                if (filled($model->{$field})) {
                    ImageOptimizer::deleteVariants($model->{$field});
                }
            }
        });
    }

    /** @return list<string> */
    protected static function optimizedImageFields(): array
    {
        return static::$optimizedImages ?? [];
    }
}