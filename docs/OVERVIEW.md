# PBM Group — Gestionale: Panoramica Generale

## Cos'è il sistema

Gestionale web interno sviluppato in **Laravel 11** per una ditta di **smaltimento e trasporto rifiuti**.  
Permette all'amministrazione di pianificare e tracciare lavori, gestire dipendenti, automezzi, carte prepagate e fondo cassa; ai dipendenti di consultare i propri turni, registrare spese e generare ricevute.

---

## Stack tecnologico

| Componente | Tecnologia |
|---|---|
| Backend | PHP 8.x / Laravel 11 |
| Frontend | Blade + Bootstrap 5 + Bootstrap Icons |
| CSS utility | Tailwind CSS (solo su alcune pagine) |
| Bundler | Vite |
| Database | MySQL |
| Datepicker | Flatpickr (con locale italiano, gestito da `public/js/date-picker-manager.js`) |
| PDF | Dompdf |
| Auth | Laravel built-in (`Auth`) |
| Mail | Laravel Mail (SMTP) |
| Timezone / Locale | `Europe/Rome` / `it` (config/app.php) |

---

## Ruoli utente

| Ruolo | Accesso |
|---|---|
| `sviluppatore` | Accesso completo, inclusa gestione utenti |
| `amministratore` | Accesso completo tranne gestione utenti |
| `dipendente` | Solo sezioni worker (`/worker/*`) |

La distinzione admin/dipendente è gestita via:
- Controlli `Auth::user()->role` dentro i controller
- Middleware `CheckWorkerRole` (`app/Http/Middleware/CheckWorkerRole.php`) applicato alle rotte `/worker/*`
- Sidebar diversa: `partials/sidebar.blade.php` (admin) e `partials/worker_sidebar.blade.php` (dipendente)

---

## Struttura delle directory principali

```
app/
  Http/
    Controllers/      ← tutti i controller
    Middleware/       ← CheckWorkerRole
  Models/             ← tutti i model Eloquent
  Helpers/
    DateHelper.php    ← helper per formattazione date (formato italiano)
  Providers/
    AppServiceProvider.php  ← direttive Blade @formatDate, @formatDateTime, @dateInput

resources/
  views/
    layouts/
      dashboard.blade.php   ← layout principale (include sidebar in base al ruolo)
    partials/
      sidebar.blade.php         ← menu amministratore
      worker_sidebar.blade.php  ← menu dipendente
    admin/        ← viste area admin
    worker/       ← viste area dipendente
    works/        ← gestione lavori
    workers/      ← anagrafica dipendenti
    vehicles/     ← automezzi
    customers/    ← clienti
    materials/    ← materiali
    deposits/     ← depositi
    warehouses/   ← sedi/magazzini
    credit_cards/ ← carte prepagate
    credit_card_assignments/  ← assegnazione carte
    credit_card_recharges/    ← ricariche carte
    pdf/          ← template PDF ricevute

database/
  migrations/     ← migration consolidate (prefisso 2025_05_26_*)

public/
  js/
    date-picker-manager.js  ← gestione flatpickr su tutti gli input[type="date"]

routes/
  web.php         ← tutte le rotte
```

---

## Convenzioni di codice

- **Date nel DB**: sempre `YYYY-MM-DD` (formato ISO)
- **Date in UI**: sempre `DD/MM/YYYY` (formato italiano, tramite `DateHelper` o Carbon)
- **Input date**: usano `input[type="date"]` con `value` in formato ISO; flatpickr li intercetta e mostra in italiano
- **Moneta**: `number_format($valore, 2, ',', '.')` — separatore decimale `,`, migliaia `.`
- **Transazioni DB**: operazioni che toccano più tabelle (es. fondo cassa) usano `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()`
- **Flash messages**: `session('success')`, `session('error')`, `session('info')` visualizzati nelle view

---

## Avvio del progetto

```bash
# Installa dipendenze
composer install
npm install

# Configura .env (copia da .env.example, imposta DB e mail)
cp .env.example .env
php artisan key:generate

# Migra il database
php artisan migrate

# Avvia server di sviluppo
php artisan serve
npm run dev
```
