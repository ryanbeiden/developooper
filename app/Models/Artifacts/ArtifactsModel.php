<?php

namespace App\Models\Artifacts;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

abstract class ArtifactsModel extends Model
{
    use Sushi;

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract public function getRows(): array;

    /**
     * @param  class-string  $schemaClass
     * @return array<string, mixed>
     */
    protected static function attributesFromSchema(object $schema, string $schemaClass): array
    {
        /** @var array<string, string> $mapper */
        $mapper = $schemaClass::attributeMap();

        /** @var array<string, string> $getters */
        $getters = $schemaClass::getters();

        $attributes = [];

        foreach ($getters as $key => $getter) {
            $column = $mapper[$key] ?? $key;
            $value = $schema->{$getter}();

            $attributes[$column] = is_array($value) ? json_encode($value) : $value;
        }

        return $attributes;
    }
}
