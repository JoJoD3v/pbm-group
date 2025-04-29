<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address','latitude','longitude'];

    // Relazione many-to-many con Material
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'deposit_material');
    }
}
