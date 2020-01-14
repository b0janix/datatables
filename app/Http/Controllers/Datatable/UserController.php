<?php

namespace App\Http\Controllers\Datatable;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends DatatableController
{
    public function build()
    {
        return User::query();
    }

    public function getDisplayableColumnNames()
    {
        return ['id', 'name', 'email', 'created_at'];
    }

    public function update($id, Request $request)
    {
        $criteria = "required";

        if($request->input('column') === 'email')
        {
            $criteria = "required|email";
        }

        $this->validate($request, [$request->input('column') => $criteria]);

        $this->builder->find($id)->update([$request->input('column') => $request->input('value')]);
    }
}
