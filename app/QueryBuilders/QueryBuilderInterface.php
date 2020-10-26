<?php

namespace App\QueryBuilders;

use Illuminate\Http\Request;

interface QueryBuilderInterface
{
    static function apply(Request $filters, $builder = null);

    public static function applyWithPaginator(Request $filters, $builder = null);
}
