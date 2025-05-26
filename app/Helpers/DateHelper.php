<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Formatta una data nel formato italiano DD/MM/YYYY
     *
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public static function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) return '-';
        
        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }
    
    /**
     * Formatta una data e ora nel formato italiano DD/MM/YYYY HH:mm
     *
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public static function formatDateTime($date, $format = 'd/m/Y H:i')
    {
        if (!$date) return '-';
        
        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }
    
    /**
     * Formatta una data per il database (YYYY-MM-DD)
     *
     * @param string|null $date
     * @return string|null
     */
    public static function formatForDatabase($date)
    {
        if (!$date) return null;
        
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Converte una data dal formato italiano DD/MM/YYYY al formato database YYYY-MM-DD
     *
     * @param string|null $date
     * @return string|null
     */    public static function convertItalianToDatabase($date)
    {
        if (!$date || $date === '-') return null;
        
        try {
            // Verifica se è già nel formato corretto del database
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date;
            }
            
            // Converte dal formato italiano DD/MM/YYYY
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                return "$year-$month-$day";
            }
            
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Converte una data e ora dal formato italiano DD/MM/YYYY HH:mm al formato database YYYY-MM-DD HH:mm:ss
     *
     * @param string|null $datetime
     * @return string|null
     */
    public static function convertItalianDateTimeToDatabase($datetime)
    {
        if (!$datetime || $datetime === '-') return null;
        
        try {
            // Verifica se è già nel formato corretto del database
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $datetime)) {
                return $datetime;
            }
            
            // Converte dal formato italiano DD/MM/YYYY HH:mm
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4}) (\d{1,2}):(\d{1,2})$/', $datetime, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                $hour = str_pad($matches[4], 2, '0', STR_PAD_LEFT);
                $minute = str_pad($matches[5], 2, '0', STR_PAD_LEFT);
                return "$year-$month-$day $hour:$minute:00";
            }
            
            return Carbon::parse($datetime)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Valida se una stringa è una data valida nel formato italiano DD/MM/YYYY
     *
     * @param string $date
     * @return bool
     */
    public static function isValidItalianDate($date)
    {
        if (!$date || $date === '-') return false;
        
        // Verifica il formato DD/MM/YYYY
        if (!preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
            return false;
        }
        
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        // Verifica range di valori
        if ($month < 1 || $month > 12) return false;
        if ($day < 1) return false;
        
        // Verifica giorni per mese
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($day > $daysInMonth) return false;
        
        return true;
    }
}
