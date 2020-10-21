<?php

namespace App\QueryBuilders;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceQueryBuilder
{
    public static function apply(Request $filters, $builder = null)
    {
        if ($builder == null) {
            $builder = (new Device)->newQuery();
        }

        // free text search
        if ($filters->query('search')) {
            $freeTextSearch = $filters->query('search');
            $builder->where('name', 'like', "%$freeTextSearch%");
        }

        return $builder;
    }

    public static function applyWithPaginator(Request $filters, $builder = null)
    {
        $builder = self::apply($filters, $builder);

        $perPage = $filters->query('perPage') ? (int) $filters->query('perPage') : 15;

        return $builder->paginate($perPage);
    }
}
