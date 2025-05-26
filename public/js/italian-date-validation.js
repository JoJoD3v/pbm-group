/**
 * Italian Date Validation
 * 
 * Funzioni di utilità per la validazione del formato data italiano (DD/MM/YYYY)
 */

/**
 * Verifica se una data è nel formato italiano DD/MM/YYYY
 * 
 * @param {string} dateStr - La stringa data da validare
 * @returns {boolean} True se la data è valida e nel formato corretto
 */
function validateItalianDate(dateStr) {
    if (!dateStr) return false;
    
    // Verifica formato DD/MM/YYYY
    const regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
    const match = dateStr.match(regex);
    
    if (!match) return false;
    
    const day = parseInt(match[1], 10);
    const month = parseInt(match[2], 10);
    const year = parseInt(match[3], 10);
    
    // Verifica range valori
    if (month < 1 || month > 12) return false;
    if (day < 1) return false;
    
    // Calcola giorni nel mese
    const daysInMonth = new Date(year, month, 0).getDate();
    if (day > daysInMonth) return false;
    
    return true;
}

/**
 * Converte una data dal formato italiano DD/MM/YYYY al formato ISO YYYY-MM-DD
 * 
 * @param {string} italianDate - Data in formato DD/MM/YYYY
 * @returns {string} Data in formato YYYY-MM-DD o stringa vuota se non valida
 */
function convertItalianToIsoDate(italianDate) {
    if (!validateItalianDate(italianDate)) return '';
    
    const parts = italianDate.split('/');
    const day = parts[0].padStart(2, '0');
    const month = parts[1].padStart(2, '0');
    const year = parts[2];
    
    return `${year}-${month}-${day}`;
}

/**
 * Converte una data dal formato ISO YYYY-MM-DD al formato italiano DD/MM/YYYY
 * 
 * @param {string} isoDate - Data in formato YYYY-MM-DD
 * @returns {string} Data in formato DD/MM/YYYY o stringa vuota se non valida
 */
function convertIsoToItalianDate(isoDate) {
    if (!isoDate) return '';
    
    const regex = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;
    const match = isoDate.match(regex);
    
    if (!match) return isoDate; // Ritorna l'input se non corrisponde al formato
    
    const year = match[1];
    const month = match[2].padStart(2, '0');
    const day = match[3].padStart(2, '0');
    
    return `${day}/${month}/${year}`;
}

// Esporta le funzioni se necessario (ES6 Modules)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateItalianDate,
        convertItalianToIsoDate,
        convertIsoToItalianDate
    };
}
