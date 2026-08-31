<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CollectionPaginator
{
    /**
     * Turn an already-loaded collection into a paginator without querying again.
     *
     * The dashboard calendar needs every event in one go, so the table below it
     * pages through that same in-memory collection instead of running a second
     * query for each page.
     */
    public static function make($items, int $perPage = 10, string $pageName = 'page'): LengthAwarePaginator
    {
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $page = LengthAwarePaginator::resolveCurrentPage($pageName);
        $perPage = max(1, $perPage);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
                'query' => request()->query(),
            ]
        );
    }
}
