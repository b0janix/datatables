<?php

namespace App\Http\Controllers\Datatable;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;


abstract class DatatableController extends Controller
{

    protected $builder;

    function __construct()
    {

        $builder = $this->build();

        if(!$builder instanceof Builder)
        {
            throw new \Exception('Entity is not an inastance of a builder');
        }

        $this->builder = $builder;

    }

    abstract public function build();

    public function index(Request $request)
    {

        return response()->json([
            'table'=>$this->builder->getModel()->getTable(),
            'displayable' => $this->getDisplayableColumnNames(),
            'data' => $this->getRecords($request)
        ]);

    }

    public function update($id, Request $request)
    {
       $this->builder->find($id)->update([$request->input('column') => $request->input('value')]);
    }

    public function getDatabaseColumnNames()
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
    }

    public function getDisplayableColumnNames()
    {
        return array_values(array_diff($this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden()));
    }

    public function getRecords(Request $request)
    {
        return $this->builder->limit($request->query('limit'))->get($this->getDisplayableColumnNames());
    }

    public function searchByColumn(Request $request)
    {
        $params = $request->input('terms');
        $limit = $request->input('limit');
        $queryBuilder = $this->builder->limit($limit);
        foreach($params as $column => $value)
        {
            $queryBuilder = $this->builder->where($column, "LIKE", "%$value%");
        }

        return $queryBuilder->get($this->getDisplayableColumnNames());

    }

}
