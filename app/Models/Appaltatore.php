<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appaltatore extends Model
{
    use HasFactory;

    protected $table = 'appaltatori';

    protected $fillable = [
        'tipo_soggetto',
        'full_name',
        'codice_fiscale',
        'ragione_sociale',
        'partita_iva',
        'address',
        'phone',
        'email',
        'latitude_appaltatore',
        'longitude_appaltatore',
        'note',
    ];
}
