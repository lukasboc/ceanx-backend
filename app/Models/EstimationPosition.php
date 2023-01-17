<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimationPosition extends Model
{
    use HasFactory;

    protected $with = ['user:id,name'];

    protected $touches = ['costEstimation'];

    public function costEstimation(){
        return $this->belongsTo(CostEstimation::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
