<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostEstimation extends Model
{
    use HasFactory;

    protected $with = ['user:id,name'];

    public function estimationPositions(){
        return $this->hasMany(EstimationPosition::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($costEstimation) { // before delete() method call this
            $costEstimation->estimationPositions()->each(function($position) {
                $position->delete();
            });
        });
    }
}
