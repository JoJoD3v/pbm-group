<?php

namespace App\Http\Controllers;

use App\Models\PezzoBordero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PezzoBorderoController extends Controller
{
    private function guardAdmin()
    {
        $role = strtolower(Auth::user()->role ?? '');
        if (! in_array($role, ['amministratore', 'sviluppatore'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $pezzi = PezzoBordero::orderBy('nome_pezzo')->get();

        return view('pezzi-bordero.index', compact('pezzi'));
    }

    public function create()
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        return view('pezzi-bordero.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $request->validate([
            'nome_pezzo' => 'required|string|max:255|unique:pezzi_bordero,nome_pezzo',
        ]);

        PezzoBordero::create(['nome_pezzo' => trim($request->nome_pezzo)]);

        return redirect()->route('admin.pezzi-bordero.index')
            ->with('success', 'Pezzo aggiunto al catalogo.');
    }

    public function edit($id)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $pezzo = PezzoBordero::findOrFail($id);

        return view('pezzi-bordero.edit', compact('pezzo'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $pezzo = PezzoBordero::findOrFail($id);

        $request->validate([
            'nome_pezzo' => 'required|string|max:255|unique:pezzi_bordero,nome_pezzo,'.$pezzo->id,
        ]);

        $pezzo->update(['nome_pezzo' => trim($request->nome_pezzo)]);

        return redirect()->route('admin.pezzi-bordero.index')
            ->with('success', 'Pezzo aggiornato.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $pezzo = PezzoBordero::findOrFail($id);
        $pezzo->delete();

        return redirect()->route('admin.pezzi-bordero.index')
            ->with('success', 'Pezzo eliminato dal catalogo.');
    }
}
