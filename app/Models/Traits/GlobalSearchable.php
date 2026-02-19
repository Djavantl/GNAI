<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait GlobalSearchable
{
    public function scopeGlobalSearch(Builder $query, ?string $term): Builder
    {
        if (!$term || !property_exists($this, 'searchable')) {
            return $query;
        }

        // normaliza texto e separa palavras
        $terms = $this->normalizeSearchTerms($term);

        return $query->where(function ($mainQuery) use ($terms) {

            foreach ($terms as $term) {

                // cada palavra deve bater em algum campo
                $mainQuery->where(function ($termQuery) use ($term) {

                    $valuesToSearch = $this->resolveAliases($term);

                    foreach ($valuesToSearch as $value) {

                        foreach ($this->searchable as $field) {

                            // campo em relação: person.name
                            if (str_contains($field, '.')) {
                                [$relation, $column] = explode('.', $field, 2);

                                $termQuery->orWhereHas($relation, function ($relQuery) use ($column, $value) {
                                    $relQuery->where($column, 'like', "{$value}%");
                                });

                            } else {
                                // campo normal
                                $termQuery->orWhere($field, 'like', "{$value}%");
                            }
                        }
                    }

                });
            }

        });
    }

    protected function normalizeSearchTerms(string $term): array
    {
        $term = mb_strtolower(trim($term));
        $term = preg_replace('/\s+/', ' ', $term);
        return explode(' ', $term);
    }

    protected function resolveAliases(string $term): array
    {
        $values = [$term];

        if (property_exists($this, 'searchAliases') &&
            isset($this->searchAliases[$term])) {
            $values = array_merge($values, $this->searchAliases[$term]);
        }

        return $values;
    }
}
