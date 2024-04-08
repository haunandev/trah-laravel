<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory, CreatedUpdatedBy;

    protected $table = 'persons';
    protected $fillable = [
        'no_induk',
        'no_urut',
        'marriage',
        'couple_id',
        'father_id',
        'mother_id',
        'fullname',
        'nik',
        'no_kk',
        'username',
        'bin',
        'garis_trah',
        'address',
        'gender',
        'kk_utama',
        'died',
        'date_of_birth',
        'place_of_birth',
        'phone_number',
        'hadir',
        'notes',
        'kota_desa',
        'photo',
        'date_of_death',
        'place_of_death',
        'created_by',
        'updated_by',
    ];
}
