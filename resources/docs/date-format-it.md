# Italian Date Format Implementation

Questa documentazione descrive l'implementazione del formato date italiano (DD/MM/YYYY) nel sistema PBM Group.

## Panoramica

Il sistema è stato configurato per utilizzare il formato date italiano in tutta l'applicazione:
- Le date vengono visualizzate nel formato `DD/MM/YYYY` (es. 26/05/2025)
- Le date con orario vengono visualizzate nel formato `DD/MM/YYYY HH:MM` (es. 26/05/2025 14:30)
- I campi di input per le date dispongono di un calendario visuale per una facile selezione
- DataTables è configurato per ordinare correttamente le date in formato italiano, considerando anche l'ora e i minuti
- È disponibile l'opzione per impostare automaticamente la data odierna come valore predefinito

## File Principali

### Backend (PHP)

- **DateHelper.php** (`app/Helpers/DateHelper.php`)
  - Classe di supporto per la formattazione delle date
  - Contiene metodi per convertire tra formato italiano e formato database
  - Fornisce direttive Blade personalizzate (`@formatDate`, `@formatDateTime`)

### Frontend (JavaScript)

- **datatables-config.js** (`public/js/datatables-config.js`)
  - Configurazione di DataTables per supportare il formato date italiano
  - Plugin per l'ordinamento corretto delle date nel formato europeo
  - Supporto separato per `date-eu` (solo data) e `date-eu-time` (data con ora) per un ordinamento preciso

- **date-format-handler.js** (`public/js/date-format-handler.js`)
  - Gestisce l'interazione con i campi di input di tipo date
  - Converte tra il formato HTML5 (`YYYY-MM-DD`) e il formato italiano (`DD/MM/YYYY`)

- **italian-date-validation.js** (`public/js/italian-date-validation.js`)
  - Fornisce funzioni per validare le date nel formato italiano
  - Contiene utility per conversione tra diversi formati date

- **date-picker-manager.js** (`public/js/date-picker-manager.js`)
  - Integra il selettore di date flatpickr con localizzazione italiana
  - Fornisce un'interfaccia visuale per la selezione delle date
  - Supporta l'impostazione della data odierna come valore predefinito

### CSS

- **date-format.css** (`public/css/date-format.css`)
  - Stili per la visualizzazione e formattazione delle date
  - Classi CSS per evidenziare i campi date (`date-column`, `datetime-column`)

## Direttive Blade

Il sistema offre diverse direttive Blade per lavorare con le date:

1. **@formatDate**: Formatta una data nel formato italiano `DD/MM/YYYY`
   ```php
   @formatDate($model->data)  // Output: 26/05/2025
   ```

2. **@formatDateTime**: Formatta una data e ora nel formato italiano `DD/MM/YYYY HH:MM`
   ```php
   @formatDateTime($model->created_at)  // Output: 26/05/2025 14:30
   ```

3. **@dateInput**: Genera un campo input di tipo date con calendario integrato
   ```php
   @dateInput('data_inizio', 'data_inizio', true, 'form-control')
   ```

   Parametri:
   - Nome del campo (obbligatorio)
   - ID del campo (se omesso, usa il nome)
   - Required (true/false)
   - Classe CSS (default: 'form-control')
   - Valore iniziale (se omesso e data_default_today=true, usa la data odierna)

## Utilizzo nelle Tabelle

Per garantire che le colonne di date siano formattate e ordinate correttamente in DataTables:

1. Aggiungere la classe CSS `date-column` alle colonne contenenti solo date:
   ```html
   <th class="date-column">Scadenza</th>
   ```

2. Aggiungere la classe CSS `datetime-column` alle colonne contenenti date con orario:
   ```html
   <th class="datetime-column">Data Creazione</th>
   ```

3. Utilizzare le direttive Blade per formattare i valori:
   ```php
   <td class="date-column">@formatDate($model->data_scadenza)</td>
   <td class="datetime-column">@formatDateTime($model->created_at)</td>
   ```

## Utilizzo nei Form

### Metodo 1: Con wrapper italian-date-input

```html
<div class="italian-date-input">
  <input type="date" name="data_esecuzione" id="data_esecuzione" class="form-control">
</div>
```

### Metodo 2: Con direttiva Blade @dateInput

```php
@dateInput('data_esecuzione', 'data_esecuzione', true, 'form-control')
```

### Metodo 3: Con data predefinita (oggi)

```html
<div class="italian-date-input">
  <input type="date" name="data_esecuzione" id="data_esecuzione" class="form-control" data-default-today="true">
</div>
```

Il sistema convertirà automaticamente questi campi per mostrare un selettore calendario con formato italiano `DD/MM/YYYY`.

## Configurazione

La configurazione del sistema è stata aggiornata per utilizzare l'italiano come lingua predefinita:

- **config/app.php**:
  - `'locale' => 'it'`
  - `'timezone' => 'Europe/Rome'`

- **File .env**:
  - `APP_LOCALE=it`
  - `APP_TIMEZONE=Europe/Rome`

## Note Tecniche

- Il sistema utilizza internamente il formato SQL `YYYY-MM-DD` per l'archiviazione delle date nel database
- La conversione tra i formati avviene automaticamente:
  - Input utente (DD/MM/YYYY) → Database (YYYY-MM-DD)
  - Database (YYYY-MM-DD) → Output utente (DD/MM/YYYY)
- La classe `DateHelper` fornisce metodi per la conversione manuale quando necessario
