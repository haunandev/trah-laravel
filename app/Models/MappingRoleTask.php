<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingRoleTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'active',
        'role_id',
        'task_id',
    ];
}
