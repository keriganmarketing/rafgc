<?php
namespace App;

use Illuminate\Http\Request;


class SearchFilters
{
    public $sort;
    public $propertyType;
    public $area;
    public $sortBy;
    public $orderBy;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sort = isset($this->request->sort) && $this->request->sort !== '' ? explode('|', $this->request->sort) : [];
        $this->propertyType = $this->request->propertyType ?? null;
        $this->area = $this->request->area ?? null;
        $this->sortBy = (isset($sort[0]) && $sort[0] != null) ? $sort[0] : 'date_modified';
        $this->orderBy = (isset($sort[1]) && $sort[1] != null) ? $sort[1] : 'desc';
    }
}
