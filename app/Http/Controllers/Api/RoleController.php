<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public $model;
    public $table_name;
    public function __construct() {
        $this->model = new Role();
        $this->table_name = 'roles';
    }
    // dataset
    public function dataset(Request $req) {$columns = $this->getTableColumns($this->table_name);
        $query = $this->model->query();


        // filter 
        $filters = request($columns);
        foreach ($filters as $filterKey => $filterVal) {
            $query->where($filterKey, $filterVal);
        }

        // search
        if ($req->search) {
            $query->where(function ($query) use ($columns, $req) {
                foreach ($columns as $colIndex => $col) {
                    if ($colIndex == 0) $query->where($col, 'LIKE', "%$req->search%");
                    else $query->orWhere($col, 'LIKE', "%$req->search%");
                }
            });
        }
        $query->orderBy('role_name');
        $paginate = $query->paginate($req->limit ?? $query->count());
        return response()->json([
            'data' => $paginate->items(),
            'total' => $paginate->total(),
            'totalPage' => $paginate->lastPage(),
        ]);
    }
    // list
    public function list(Request $req)
    {
        $columns = $this->getTableColumns($this->table_name);

        $query = $this->model->query();


        // filter 
        $filters = request($columns);
        foreach ($filters as $filterKey => $filterVal) {
            $query->where($filterKey, $filterVal);
        }

        // search
        if ($req->search) {
            $query->where(function ($query) use ($columns, $req) {
                foreach ($columns as $colIndex => $col) {
                    if ($colIndex == 0) $query->where($col, 'LIKE', "%$req->search%");
                    else $query->orWhere($col, 'LIKE', "%$req->search%");
                }
            });
        }

        // sort
        if ($req->order) {
            $query->orderBy($req->order, $req->sort);
        }
        $paginate = $query->paginate($req->limit ?? 10);
        return response()->json([
            'data' => $paginate->items(),
            'total' => $paginate->total(),
            'totalPage' => $paginate->lastPage(),
        ]);
    }
    // create
    public function create(Request $req)
    {
        $valid = Validator::make($req->all(), [
            'name' => "required|unique:$this->table_name,name",
        ]);
        if ($valid->fails()) {
            return response()->json([
                'message' => $valid->errors()->first(),
                'success' => false
            ], 400);
        }
        $query = $this->model->create($req->all());
        return response()->json([
            'success' => true,
            'message' => 'Sukses menambah data.',
            'data' => $query
        ]);
    }
    // show
    public function show($id)
    {
        $valid = Validator::make(['id' => $id], [
            'id' => "required|exists:$this->table_name,id",
        ]);
        if ($valid->fails()) {
            return response()->json([
                'message' => $valid->errors()->first(),
                'success' => false
            ], 400);
        }
        $query = $this->model->find($id);
        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil data.',
            'data' => $query
        ]);
    }
    // update
    public function update(Request $req)
    {
        $valid = Validator::make($req->all(), [
            'id' => "required|exists:$this->table_name,id",
        ]);
        if ($valid->fails()) {
            return response()->json([
                'message' => $valid->errors()->first(),
                'success' => false
            ], 400);
        }
        $checkNameExists = $this->model->where(['role_name' => $req->role_name, ['id', '!=', $req->id]])->first();
        if ($checkNameExists) {
            return response()->json([
                'message' => 'Nama sudah ada!',
                'success' => false
            ], 400);
        }
        $query = $this->model->find($req->id);
        $query->update($req->all());
        return response()->json([
            'success' => true,
            'message' => 'Sukses mengubah data.',
            'data' => $query
        ]);
    }
    // delete
    public function delete(Request $req)
    {
        $valid = Validator::make($req->all(), [
            'id' => "required|exists:$this->table_name,id",
        ]);
        if ($valid->fails()) {
            return response()->json([
                'message' => $valid->errors()->first(),
                'success' => false
            ], 400);
        }
        $query = $this->model->find($req->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sukses menghapus data.',
            'data_id' => $req->id
        ]);
    }
}
