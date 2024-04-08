<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{
    public $model;
    public $table_name;
    public function __construct() {
        $this->model = new Person();
        $this->table_name = 'persons';
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
        $query->orderBy('name');
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

    // sync data
    public function syncData(Request $req) {
        $old_data = DB::table('t_family')->orderBy('Nomor_Induk')->get()->toArray();
        $old_data_filtered = array_filter($old_data, function ($var) {
            return ($var->Nomor_Induk && $var->Nama_Lengkap && $var->Nama_Lengkap != '.....');
        });
        $new_table_column = Schema::getColumnListing('persons');

        foreach ($old_data_filtered as $key => $value) {
            $convertNoInduk = str_split($value->Nomor_Induk);
            foreach ($convertNoInduk as $noIndukKey => $noInduk) {
                $convertNoInduk[$noIndukKey] = str_pad($noInduk, 2, '0', STR_PAD_LEFT);
            }
            $convertNoInduk = implode("-", $convertNoInduk);
            $checkExist = $this->model->where([
                'fullname' => $value->Nama_Lengkap,
                'no_induk' => $convertNoInduk
            ])->first();
            $personData = [
                "address" => $value->Alamat,
                "bin" => $value->Bin,
                "couple_id",
                "date_of_birth" => $value->Tanggal_Lahir,
                "date_of_death" => $value->Tanggal_Meninggal,
                "died" => $value->Meninggal,
                "father_id",
                "fullname" => $value->Nama_Lengkap,
                "garis_trah" => $value->Garis_Trah,
                "gender" => $value->Gender,
                "hadir" => $value->Hadir,
                "kk_utama" => $value->KK_Utama,
                "kota_desa" => $value->KotaDesa,
                "marriage" => $value->Marriage,
                "mother_id",
                "nik",
                "no_induk" => $convertNoInduk,
                "no_kk",
                "no_urut" => $value->Nomor_Urut,
                "notes" => $value->Catatan,
                "phone_number" => $value->Nomor_HP,
                "photo",
                "place_of_birth" => $value->Tempat_Lahir,
                "place_of_death" => $value->Tempat_Meninggal,
                "username" => $value->Panggilan
            ];
            if ($personData['date_of_birth'] == '0000-00-00') $personData['date_of_birth'] = null; 
            if ($personData['date_of_death'] == '0000-00-00') $personData['date_of_death'] = null; 

            if (!$checkExist) {
                $this->model->create($personData);
            } else $this->model->update($personData);
        }
        
        $persons = $this->model->get();
        $res = [
            'new_table_column' => $new_table_column,
            'old_data' => count($old_data),
            'old_data_filtered' => count($old_data_filtered),
            'persons' => count($persons),
            'old_data_filtered_example' => $old_data_filtered[1] ?? null,
        ];
        return response()->json($res);
    }
}