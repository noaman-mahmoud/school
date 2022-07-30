<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name'];

    public function order() {
        return $this->hasOne(Student::class, 'school_id')->orderBy('order', 'asc');
    }
}
