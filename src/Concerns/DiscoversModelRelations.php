<?php

namespace WorkDoneRight\DeletionGuard\Concerns;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;
use ReflectionMethod;

trait DiscoversModelRelations
{
    protected function discoverRelations(): array
    {
        $cacheKey = config('deletion-guard.cache.prefix').static::class;

        return config('deletion-guard.cache.enabled')
            ? Cache::remember($key, config('deletion-guard.cache.ttl'), fn () => $this->reflectRelations())
            : $this->reflectRelations();
    }

    protected function reflectRelations(): array
    {
        $relations = [];
        $class = new ReflectionClass($this);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getNumberOfParameters() !== 0) {
                continue;
            }

            if ($method->getDeclaringClass()->getName() !== static::class) {
                continue;
            }

            try {
                $result = $this->{$method->getName()}();
                if ($result instanceof Relation) {
                    $relations[$method->getName()] = $method;
                }
            } catch (\Throwable) {
            }
        }

        return $relations;
    }
}
