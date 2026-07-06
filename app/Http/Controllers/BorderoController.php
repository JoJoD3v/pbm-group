<?php

namespace App\Http\Controllers;

use App\Models\Bordero;
use App\Models\PezzoBordero;
use App\Models\Work;
use App\Models\Worker;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BorderoController extends Controller
{
    /**
     * Mostra il form di creazione/modifica del borderò per un lavoro.
     */
    public function edit($workId)
    {
        $work = Work::with(['customer', 'bordero.pezzi'])->findOrFail($workId);

        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        if ($role === 'dipendente') {
            $worker = $user->worker;
            if (! $worker) {
                $worker = Worker::where('worker_email', $user->email)->first();
            }
            if (! $worker || ! $work->workers->contains($worker->id)) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Non sei autorizzato ad accedere a questo lavoro.');
            }
        } elseif (! in_array($role, ['amministratore', 'sviluppatore'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $bordero = $work->bordero ?? new Bordero([
            'work_id' => $work->id,
            'status' => 'In Sospeso',
        ]);

        $catalogoPezzi = PezzoBordero::orderBy('nome_pezzo')->get();

        $saveRoute = $role === 'dipendente'
            ? route('worker.bordero.save', $work->id)
            : route('admin.bordero.save', $work->id);

        $pdfRoute = $role === 'dipendente'
            ? route('worker.bordero.pdf', $work->id)
            : route('bordero.pdf', $work->id);

        $sendRoute = $role === 'dipendente'
            ? route('worker.bordero.send', $work->id)
            : route('admin.bordero.send', $work->id);

        return view('bordero.form', compact('work', 'bordero', 'catalogoPezzi', 'saveRoute', 'pdfRoute', 'sendRoute'));
    }

    /**
     * Salva (crea o aggiorna) il borderò per un lavoro.
     */
    public function save(Request $request, $workId)
    {
        $work = Work::with('workers')->findOrFail($workId);

        $user = Auth::user();
        $role = strtolower($user->role ?? '');
        $worker = $user->worker ?: Worker::where('worker_email', $user->email)->first();

        if ($role === 'dipendente') {
            if (! $worker || ! $work->workers->contains($worker->id)) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Non sei autorizzato ad accedere a questo lavoro.');
            }
        } elseif (! in_array($role, ['amministratore', 'sviluppatore'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Completo,In Sospeso,Non realizzabile',
            'note_tecniche' => 'nullable|string',
            'pezzi' => 'required|array|min:1',
            'pezzi.*.nome_pezzo' => 'required|string|max:255',
            'pezzi.*.quantita' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($work, $validated, $worker) {
            $bordero = Bordero::firstOrNew(['work_id' => $work->id]);
            $bordero->worker_id = $worker?->id;
            $bordero->status = $validated['status'];
            $bordero->note_tecniche = $validated['note_tecniche'] ?? null;
            $bordero->save();

            $bordero->pezzi()->delete();

            foreach ($validated['pezzi'] as $riga) {
                $nomePezzo = trim($riga['nome_pezzo']);
                $pezzoCatalogo = PezzoBordero::firstOrCreate(['nome_pezzo' => $nomePezzo]);

                $bordero->pezzi()->create([
                    'pezzo_bordero_id' => $pezzoCatalogo->id,
                    'nome_pezzo' => $pezzoCatalogo->nome_pezzo,
                    'quantita' => $riga['quantita'],
                ]);
            }
        });

        $redirectRoute = $role === 'dipendente' ? 'worker.jobs.show' : 'works.show';

        return redirect()->route($redirectRoute, $work->id)
            ->with('success', 'Borderò salvato con successo.');
    }

    /**
     * Esporta il borderò in PDF.
     */
    public function downloadPDF($workId)
    {
        $work = Work::with(['customer', 'bordero.pezzi', 'workers'])->findOrFail($workId);

        $user = Auth::user();
        $role = strtolower($user->role ?? '');
        $isAdmin = in_array($role, ['amministratore', 'sviluppatore']);
        $worker = $user->worker ?: Worker::where('worker_email', $user->email)->first();
        $isAssignedWorker = $worker && $work->workers->contains($worker->id);

        if (! $isAdmin && ! $isAssignedWorker) {
            abort(403, 'Non sei autorizzato ad accedere a questo borderò.');
        }

        if (! $work->bordero) {
            abort(404, 'Nessun borderò trovato per questo lavoro.');
        }

        $fileName = 'bordero_lavoro_'.$work->id.'.pdf';

        return response($this->renderPDF($work), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Invia il borderò in PDF via email al cliente o all'appaltatore del lavoro.
     */
    public function sendEmail($workId)
    {
        $work = Work::with(['customer', 'appaltatore', 'bordero.pezzi', 'workers'])->findOrFail($workId);

        $user = Auth::user();
        $role = strtolower($user->role ?? '');
        $isAdmin = in_array($role, ['amministratore', 'sviluppatore']);
        $worker = $user->worker ?: Worker::where('worker_email', $user->email)->first();
        $isAssignedWorker = $worker && $work->workers->contains($worker->id);

        if (! $isAdmin && ! $isAssignedWorker) {
            return redirect()->route('dashboard')
                ->with('error', 'Non sei autorizzato ad accedere a questo borderò.');
        }

        if (! $work->bordero) {
            return back()->with('error', 'Nessun borderò trovato per questo lavoro.');
        }

        $recipient = $work->customer ?? $work->appaltatore;

        if (! $recipient || ! $recipient->email) {
            return back()->with('error', 'Nessun indirizzo email impostato per il cliente o l\'appaltatore di questo lavoro.');
        }

        $bordero = $work->bordero;
        $fileName = 'bordero_lavoro_'.$work->id.'.pdf';
        $pdfPath = storage_path('app/temp/'.$fileName);

        if (! file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        file_put_contents($pdfPath, $this->renderPDF($work));

        try {
            Mail::send('emails.bordero', [
                'work' => $work,
                'bordero' => $bordero,
                'recipient' => $recipient,
            ], function ($message) use ($recipient, $work, $pdfPath, $fileName) {
                $message->to($recipient->email)
                    ->subject('Borderò lavoro #'.$work->id);

                $message->attach($pdfPath, [
                    'as' => $fileName,
                    'mime' => 'application/pdf',
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Errore invio email borderò: '.$e->getMessage());

            return back()->with('error', 'Errore durante l\'invio dell\'email. Riprova più tardi.');
        } finally {
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }

        return back()->with('success', 'Borderò inviato con successo a '.$recipient->email.'.');
    }

    /**
     * Renderizza il PDF del borderò e ritorna i byte del documento.
     */
    private function renderPDF(Work $work): string
    {
        $bordero = $work->bordero;

        $html = view('pdf.bordero', compact('work', 'bordero'))->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
