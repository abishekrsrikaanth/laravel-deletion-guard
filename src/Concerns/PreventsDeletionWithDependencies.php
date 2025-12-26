<?php

namespace WorkDoneRight\DeletionGuard\Concerns;

use Illuminate\Database\Eloquent\Relations\Relation;
use WorkDoneRight\DeletionGuard\Exceptions\DeletionBlockedException;

trait PreventsDeletionWithDependencies
{
    use DiscoversModelRelations;

    protected static function bootPreventsDeletionWithDependencies()
    {
        static::deleting(function ($model) {
            if (
                method_exists($model, 'isForceDeleting') &&
                $model->isForceDeleting() &&
                config('deletion-guard.allow_force_delete')
            ) {
                return;
            }

            $blockers = $model->deletionBlockers();

            if (! empty($blockers)) {
                throw DeletionBlockedException::withBlockers($blockers);
            }
        });
    }

    public function deletionBlockers(): array
    {
        return config('deletion-guard.mode') === 'docblock'
            ? $this->docblockBlockers()
            : $this->explicitBlockers();
    }

    protected function explicitBlockers(): array
    {
        if (! method_exists($this, 'deletionDependencies')) {
            return [];
        }

        $blockers = [];

        foreach ($this->deletionDependencies() as $relation => $config) {
            if (is_int($relation)) {
                $relation = $config;
                $config = [];
            }

            $query = $this->$relation();

            if (! $query instanceof Relation) {
                continue;
            }

            if (($config['withTrashed'] ?? false) && method_exists($query, 'withTrashed')) {
                $query = $query->withTrashed();
            }

            if ($query->exists()) {
                $blockers[] = [
                    'relation' => $relation,
                    'message' => $config['message']
                        ?? "Cannot delete due to related {$relation}.",
                ];
            }
        }

        return $blockers;
    }

    protected function docblockBlockers(): array
    {
        $blockers = [];

        foreach ($this->discoverRelations() as $name => $method) {
            $doc = $method->getDocComment() ?: '';

            if (! str_contains($doc, '@deleteBlocker')) {
                continue;
            }

            $query = $this->$name();

            if ($query instanceof Relation && $query->exists()) {
                preg_match('/@deleteMessage\s+(.*)/', $doc, $matches);

                $blockers[] = [
                    'relation' => $name,
                    'message' => $matches[1] ?? $this->friendlyMessage($name),
                ];
            }
        }

        return $blockers;
    }

    protected function friendlyMessage(string $relation): string
    {
        return 'Cannot delete because related '.
            str_replace('_', ' ', \Str::snake($relation)).
            ' exist.';
    }
}
