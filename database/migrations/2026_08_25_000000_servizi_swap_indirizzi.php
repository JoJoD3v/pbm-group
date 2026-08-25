<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Per i lavori "Servizi" l'indirizzo dell'intervento era salvato in
     * indirizzo_partenza. Ora indirizzo_partenza ospita il luogo di partenza
     * e l'intervento passa su indirizzo_destinazione.
     */
    public function up(): void
    {
        DB::table('works')
            ->where('tipo_lavoro', 'Servizi')
            ->update([
                'indirizzo_destinazione' => DB::raw('indirizzo_partenza'),
                'latitude_destinazione' => DB::raw('latitude_partenza'),
                'longitude_destinazione' => DB::raw('longitude_partenza'),
                'indirizzo_partenza' => null,
                'latitude_partenza' => null,
                'longitude_partenza' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('works')
            ->where('tipo_lavoro', 'Servizi')
            ->update([
                'indirizzo_partenza' => DB::raw('indirizzo_destinazione'),
                'latitude_partenza' => DB::raw('latitude_destinazione'),
                'longitude_partenza' => DB::raw('longitude_destinazione'),
                'indirizzo_destinazione' => null,
                'latitude_destinazione' => null,
                'longitude_destinazione' => null,
            ]);
    }
};
