<?php

namespace App\Traits;

trait PaginationTrait
{

    public function paginationModel($col)
    {
        return [
            'total_items'   => $col->total(),
            'count_items'   => (int) $col->count(),
            'per_page'      => $col->perPage(),
            'total_pages'   => $col->lastPage(),
            'current_page'  => $col->currentPage(),
        ];

    }
}
