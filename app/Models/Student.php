<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name','school_id','order'];

    /** function school . */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public static function boot() {
        parent::boot();

        self::creating(function ($model) {
            $lastId = self::where('school_id',$model->school_id)->max('order') ?? 0;
            $model->order =  ($lastId + 1);
        });

        self::updating(function ($model) {
            $school = $model->getOriginal('school_id');
            if ($school != $model->school_id){
                $lastId = self::where('school_id',$model->school_id)->max('order') ?? 0;
                $model->order =  ($lastId + 1);
            }
        });

    }
}
