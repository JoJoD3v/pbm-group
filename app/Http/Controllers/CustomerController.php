<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    // Elenco dei clienti
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    // Mostra il form per creare un nuovo Customer
    public function create()
    {
        return view('customers.create');
    }

    // Salva un nuovo Customer
    public function store(Request $request)
    {
        // Validazione (personalizza le regole in base al tipo)
        if ($request->customer_type == 'fisica') {
            $request->validate([
                'customer_type'  => 'required|in:fisica,giuridica',
                'full_name'      => 'required|string|max:255',
                'address'        => 'required|string|max:255',
                'phone'          => 'required|string|max:50',
                'email'          => 'required|email|max:255',
                'codice_fiscale' => 'required|string|max:50',
                'latitude_customer'       => 'nullable|numeric',
                'longitude_customer'      => 'nullable|numeric',                 
            ]);
        } else { // giuridica
            $request->validate([
                'customer_type'    => 'required|in:fisica,giuridica',
                'ragione_sociale'  => 'required|string|max:255',
                'address'          => 'required|string|max:255',
                'phone'            => 'required|string|max:50',
                'email'            => 'required|email|max:255',
                'partita_iva'      => 'required|string|max:50',
                'latitude_customer'       => 'nullable|numeric',
                'longitude_customer'      => 'nullable|numeric',                    
            ]);
        }

        Customer::create($request->all());
        return redirect()->route('customers.index')
                         ->with('success', 'Cliente creato con successo.');
    }

    // Mostra il form per modificare un Customer
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // Aggiorna un Customer
    public function update(Request $request, Customer $customer)
    {
        if ($request->customer_type == 'fisica') {
            $request->validate([
                'customer_type'  => 'required|in:fisica,giuridica',
                'full_name'      => 'required|string|max:255',
                'address'        => 'required|string|max:255',
                'phone'          => 'required|string|max:50',
                'email'          => 'required|email|max:255',
                'codice_fiscale' => 'required|string|max:50',
                'latitude_customer'       => 'nullable|numeric',
                'longitude_customer'      => 'nullable|numeric',                  
            ]);
        } else { // giuridica
            $request->validate([
                'customer_type'    => 'required|in:fisica,giuridica',
                'ragione_sociale'  => 'required|string|max:255',
                'address'          => 'required|string|max:255',
                'phone'            => 'required|string|max:50',
                'email'            => 'required|email|max:255',
                'partita_iva'      => 'required|string|max:50',
                'latitude_customer'       => 'nullable|numeric',
                'longitude_customer'      => 'nullable|numeric',                  
            ]);
        }

        $customer->update($request->all());
        return redirect()->route('customers.index')
                         ->with('success', 'Cliente aggiornato con successo.');
    }

    // Elimina un Customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')
                         ->with('success', 'Cliente eliminato con successo.');
    }
}
