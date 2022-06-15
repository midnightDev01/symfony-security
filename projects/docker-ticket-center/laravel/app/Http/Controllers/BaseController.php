<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    protected string $items, $query, $sort, $sortBy;
    protected ?\DateTime $from, $to;
    protected const DEFAULT_ITEMS_PER_PAGE = 20;
    protected const DEFAULT_SORT           = 'id';
    protected const DEFAULT_SORT_DIRECTION = 'ASC';

    public function __construct(Request $request)
    {
        $this->items  = $request->get('items') ?? self::DEFAULT_ITEMS_PER_PAGE;
        $this->query  = $request->get('query') ?? '';
        $this->sort   = $request->get('sort') ?? self::DEFAULT_SORT_DIRECTION;
        $this->sortBy = $request->get('sortBy') ?? self::DEFAULT_SORT;
        $this->from   = $request->get('from') ? Carbon::createFromFormat('Y-m-d', $request->get('from'))->startOfDay() : null;
        $this->to     = $request->get('to') ? Carbon::createFromFormat('Y-m-d', $request->get('to'))->endOfDay() : null;
    }
}
