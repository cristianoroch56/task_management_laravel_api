<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "tasks";

    protected $fillable = [
        'assignee',
        'name',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['status_name'];

    const STATUS = [
        1 => 'New',
        2 => 'In Progress',
        3 => 'On Review',
        4 => 'Completed',
    ];

    /**
     * Get the status name.
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return (!empty(self::STATUS[$this->status])) ? self::STATUS[$this->status] : null;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'assignee')->select('id', 'username', 'email', 'role');
    }
}
