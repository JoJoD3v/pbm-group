<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name','eer_code'];

    public function deposits(){
        return $this->belongsToMany(Deposit::class, 'deposit_material');
    }

}
