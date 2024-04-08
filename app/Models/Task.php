<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_code',
        'task_name',
        'task_group',
        'description',
    ];

    public function mapping_role_tasks()
    {
        return $this->hasMany(MappingRoleTask::class, 'task_id', 'id');
    }
}
