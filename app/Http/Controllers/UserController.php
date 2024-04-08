<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $model;
    public $table_name;
    public function __construct() {
        $this->model = new User();
        $this->table_name = 'users';
    }
    // dataset
    public function dataset(Request $req) {
        $data = $this->model->all();
        return response()->json([
            'data' => $data,
            'total' => count($data),
            'totalPage' => 1
        ]);
    }
    // list
    public function list(Request $req)
    {
        $columns = $this->getTableColumns($this->table_name);

        $query = $this->model->query();

        $query->with('role');
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
        $order_explode = explode('.', $req->order);
        if ($req->order) {
            if ($req->order && count($order_explode) == 1) $query->orderBy($req->order, $req->sort);
            else if (count($order_explode) == 2) {
                $query->join("$order_explode[0]s", "$this->table_name.$order_explode[0]_id", '=', "$order_explode[0]s.id")
                    ->orderBy("$order_explode[0]s.$order_explode[1]", $req->sort);
            }
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
        $datas = $req->all();
        $query = $this->model->create($datas);
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
        $query = $this->model->where('id', $id)->first();
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
        $datas = $req->all();
        $query = $this->model->find($req->id);
        $query->update($datas);
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
