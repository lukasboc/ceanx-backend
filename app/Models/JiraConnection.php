<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JiraConnection extends Model
{
    use HasFactory;

    protected $with = ['user:id,name'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
