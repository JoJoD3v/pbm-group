# Aggiornamenti Design PBM Group - Gestionale

## Panoramica
Questo documento descrive tutti gli aggiornamenti di design applicati al gestionale Laravel per PBM Group, una ditta di trasporto e smaltimento rifiuti.

## Modifiche Implementate

### 1. 🎨 **Nuova Palette Colori**
Implementata la nuova palette colori aziendale:

#### Colori Principali
- **Arancio Mezzo** `#D17825` - Pulsanti primari, highlights attivi
- **Marrone Scuro** `#3B2A1C` - Header, footer, testi forti
- **Grigio Carbone** `#1E1E1E` - Sfondo scuro, testi principali
- **Grigio Cemento** `#4C4C4C` - Testi secondari, icone, bordi

#### Colori di Supporto
- **Grigio Roccia** `#9CA3AF` - Linee divisorie, bordi leggeri
- **Arancio Chiaro** `#F4A261` - Hover, notifiche leggere, badge
- **Bianco Sporcato** `#F9FAFB` - Sfondo principale (non abbagliante)
- **Blu Grafite** `#1F2937` - Pulsanti secondari, sfondo nav bar

### 2. 🖼️ **Logo Aziendale**
- **Inserito logo** `public/img/logo/logo.jpg` nella sidebar principale
- **Sostituita icona CSS** `fas fa-laugh-wink` con il logo aziendale
- **Aggiornato branding** da "Gestionale" a "PBM Group"
- **Applicato a entrambe le sidebar** (admin e dipendenti)

### 3. 🔤 **Font Roboto Regular**
- **Implementato Google Fonts Roboto** per migliore leggibilità
- **Applicato a tutti i layout** (dashboard, auth, app)
- **Configurato come font principale** con fallback appropriati
- **Pesi disponibili**: 300, 400, 500, 700 (normale e corsivo)

### 4. 🔐 **Pagina Login Rinnovata**
- **Creato nuovo layout** `resources/views/layouts/auth.blade.php`
- **Design professionale** con layout a due colonne
- **Logo prominente** nella sezione sinistra
- **Form moderno** con floating labels e icone
- **Gradiente di sfondo** con i colori aziendali
- **Responsive design** per dispositivi mobili

### 5. 🎯 **Componenti UI Aggiornati**

#### Sidebar
- **Gradiente personalizzato** Blu Grafite → Marrone Scuro
- **Hover effects** con Arancio Chiaro
- **Logo responsive** con dimensioni adattive

#### Pulsanti
- **Primari**: Arancio Mezzo con hover Arancio Chiaro
- **Secondari**: Blu Grafite con hover Grigio Cemento
- **Effetti**: Transform e box-shadow su hover

#### Cards
- **Border radius** aumentato a 12px
- **Hover effects** con elevazione
- **Bordi** con Grigio Roccia

#### Form Elements
- **Focus states** con Arancio Mezzo
- **Border radius** arrotondati
- **Transizioni** fluide

#### Tables
- **Header** con sfondo Bianco Sporcato
- **Striped rows** con trasparenza
- **Bordi** con Grigio Roccia

## File Modificati

### CSS
- `public/css/style.css` - **Completamente riscritto** con nuova palette e componenti

### Layout
- `resources/views/layouts/dashboard.blade.php` - Aggiunto CSS personalizzato e font Roboto
- `resources/views/layouts/app.blade.php` - Aggiunto font Roboto e CSS personalizzato
- `resources/views/layouts/auth.blade.php` - **Nuovo layout** per autenticazione

### Sidebar
- `resources/views/partials/sidebar.blade.php` - Logo aziendale e branding
- `resources/views/partials/worker_sidebar.blade.php` - Logo aziendale e branding

### Pagine
- `resources/views/login.blade.php` - **Completamente ridisegnata** con nuovo layout

## Caratteristiche Tecniche

### Variabili CSS
Utilizzate CSS Custom Properties per gestione centralizzata dei colori:
```css
:root {
  --arancio-mezzo: #D17825;
  --marrone-scuro: #3B2A1C;
  --grigio-carbone: #1E1E1E;
  /* ... altre variabili */
}
```

### Responsive Design
- **Breakpoint mobile**: 768px
- **Logo adattivo** nelle sidebar
- **Layout responsive** per login
- **Componenti scalabili**

### Accessibilità
- **Contrasti** conformi alle linee guida WCAG
- **Focus states** visibili
- **Hover states** intuitivi
- **Font leggibile** (Roboto)

## Compatibilità
- **Browser moderni** con supporto CSS Custom Properties
- **Bootstrap 5.3+** compatibile
- **Font Awesome 6.5+** per icone
- **Responsive** su tutti i dispositivi

## Note per Sviluppi Futuri
1. **Estendere la palette** per nuovi componenti se necessario
2. **Mantenere consistenza** con le variabili CSS definite
3. **Testare accessibilità** per nuove funzionalità
4. **Ottimizzare performance** caricamento font se necessario

---
*Aggiornamenti completati il: [Data corrente]*
*Versione: 1.0*
