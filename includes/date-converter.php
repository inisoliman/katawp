<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Date Converter
 * Handles conversion between Coptic and Gregorian calendars
 * Based on original Katamars calendar conversion algorithms
 */

class KataWP_Date_Converter {

    /**
     * Convert Coptic date to Gregorian date
     * Coptic date format: "M/D/Y" e.g., "9/28/1719"
     */
    public static function coptic_to_gregorian($coptic_date) {
        if (!self::is_valid_coptic_date($coptic_date)) {
            return false;
        }
        
        $certain_coptic_date = "9/28/1719";
        $certain_gregorian_date = explode("/", "6/5/2003");
        
        $days = self::coptic_date_diff($coptic_date, $certain_coptic_date);
        $gregorian_timestamp = mktime(0, 0, 0, $certain_gregorian_date[0], $certain_gregorian_date[1] + $days, $certain_gregorian_date[2]);
        
        return date("n/j/Y", $gregorian_timestamp);
    }
    
    /**
     * Validate if date is in correct Coptic date format
     */
    public static function is_valid_coptic_date($coptic_date) {
        $coptic_parts = explode("/", $coptic_date);
        
        if (count($coptic_parts) !== 3) {
            return false;
        }
        
        $month = $coptic_parts[0];
        $day = $coptic_parts[1];
        $year = $coptic_parts[2];
        
        // Check if all parts are numeric integers
        if (!is_numeric($month) || !is_numeric($day) || !is_numeric($year)) {
            return false;
        }
        
        $month = intval($month);
        $day = intval($day);
        $year = intval($year);
        
        // Validate ranges
        if ($month <= 0 || $month > 13 || $day <= 0 || $day > 30) {
            return false;
        }
        
        // 13th month (Epagomenae) has variable length
        if ($month === 13) {
            $max_days = self::is_coptic_leap_year($year) ? 6 : 5;
            if ($day > $max_days) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Calculate day difference between two Coptic dates
     */
    public static function coptic_date_diff($date1, $date2) {
        if (!self::is_valid_coptic_date($date1) || !self::is_valid_coptic_date($date2)) {
            return false;
        }
        
        $parts1 = explode("/", $date1);
        $parts2 = explode("/", $date2);
        
        $month1 = intval($parts1[0]);
        $day1 = intval($parts1[1]);
        $year1 = intval($parts1[2]);
        
        $month2 = intval($parts2[0]);
        $day2 = intval($parts2[1]);
        $year2 = intval($parts2[2]);
        
        $diff = 0;
        
        if ($year1 > $year2) {
            if (($year1 - $year2) > 1) {
                while (($year1 - $year2) > 1) {
                    $diff += self::is_coptic_leap_year($year2) ? 366 : 365;
                    $year2++;
                }
                $date2 = $month2 . "/" . $day2 . "/" . $year2;
            }
            while ($date1 !== $date2) {
                $date2 = self::coptic_date_add($date2, 1);
                $diff++;
            }
        } elseif ($year1 < $year2) {
            if (($year2 - $year1) > 1) {
                while (($year2 - $year1) > 1) {
                    $diff += self::is_coptic_leap_year($year1) ? 366 : 365;
                    $year1++;
                }
                $date1 = $month1 . "/" . $day1 . "/" . $year1;
            }
            while ($date1 !== $date2) {
                $date1 = self::coptic_date_add($date1, 1);
                $diff++;
            }
            $diff *= -1;
        } else {
            if (($month1 > $month2) || (($month1 === $month2) && ($day1 > $day2))) {
                while ($date1 !== $date2) {
                    $date2 = self::coptic_date_add($date2, 1);
                    $diff++;
                }
            } elseif (($month1 < $month2) || (($month1 === $month2) && ($day1 < $day2))) {
                while ($date1 !== $date2) {
                    $date1 = self::coptic_date_add($date1, 1);
                    $diff++;
                }
                $diff *= -1;
            }
        }
        
        return $diff;
    }
    
    /**
     * Add days to a Coptic date
     */
    public static function coptic_date_add($coptic_date, $days) {
        if (!self::is_valid_coptic_date($coptic_date)) {
            return false;
        }
        
        $parts = explode("/", $coptic_date);
        $month = intval($parts[0]);
        $day = intval($parts[1]);
        $year = intval($parts[2]);
        
        if ($days > 0) {
            for ($i = 0; $i < $days; $i++) {
                $day++;
                if ($day === 31) {
                    $day = 1;
                    $month++;
                }
                if ((($month === 13) && ($day === 7) && self::is_coptic_leap_year($year)) ||
                    (($month === 13) && ($day === 6) && !self::is_coptic_leap_year($year))) {
                    $day = 1;
                    $month = 1;
                    $year++;
                }
            }
        } elseif ($days < 0) {
            for ($i = 0; $i > $days; $i--) {
                $day--;
                if ($day === 0) {
                    $day = 30;
                    $month--;
                }
                if (($month === 0) && ($day === 30)) {
                    if (self::is_coptic_leap_year($year - 1)) {
                        $day = 6;
                    } else {
                        $day = 5;
                    }
                    $month = 13;
                    $year--;
                }
            }
        }
        
        return $month . "/" . $day . "/" . $year;
    }
    
    /**
     * Get the Coptic day from a Coptic date string
     */
    public static function get_coptic_day($coptic_date) {
        if (!self::is_valid_coptic_date($coptic_date)) {
            return false;
        }
        $parts = explode("/", $coptic_date);
        return intval($parts[1]);
    }
    
    /**
     * Get the Coptic month from a Coptic date string
     */
    public static function get_coptic_month($coptic_date) {
        if (!self::is_valid_coptic_date($coptic_date)) {
            return false;
        }
        $parts = explode("/", $coptic_date);
        return intval($parts[0]);
    }
    
    /**
     * Get the Coptic year from a Coptic date string
     */
    public static function get_coptic_year($coptic_date) {
        if (!self::is_valid_coptic_date($coptic_date)) {
            return false;
        }
        $parts = explode("/", $coptic_date);
        return intval($parts[2]);
    }
    
    /**
     * Check if Coptic year is a leap year
     * Coptic leap years: every 4 years, year+1 divisible by 4
     */
    public static function is_coptic_leap_year($year) {
        $adjusted_year = $year + 1;
        return ($adjusted_year % 4 === 0);
    }
    
    /**
     * Check if Gregorian year is a leap year
     */
    public static function is_gregorian_leap_year($year) {
        return (($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0));
    }
    
    /**
     * Get current date in both Coptic and Gregorian calendars
     */
    public static function get_current_dates() {
        $gregorian = date("n/j/Y");
        $gregorian_timestamp = strtotime($gregorian);
        
        $certain_coptic_date = "9/28/1719";
        $certain_gregorian = mktime(0, 0, 0, 6, 5, 2003);
        
        $days_diff = ($gregorian_timestamp - $certain_gregorian) / 86400;
        
        // Convert to Coptic date by adding days
        $coptic = $certain_coptic_date;
        for ($i = 0; $i < $days_diff; $i++) {
            $coptic = self::coptic_date_add($coptic, 1);
        }
        
        return array(
            'gregorian' => $gregorian,
            'coptic' => $coptic
        );
    }
    
    /**
     * Get Coptic month name
     */
    public static function get_coptic_month_name($month, $lang = 'ar') {
        $months_ar = array(
            1 => 'توت', 2 => 'بابة', 3 => 'هاتور', 4 => 'كيهك',
            5 => 'طوبة', 6 => 'أمشير', 7 => 'برمهات', 8 => 'برمودة',
            9 => 'بشنس', 10 => 'بؤونة', 11 => 'أبيب', 12 => 'مسرى',
            13 => 'نسيء'
        );
        
        $months_en = array(
            1 => 'Thout', 2 => 'Baba', 3 => 'Hator', 4 => 'Kiahk',
            5 => 'Toba', 6 => 'Amshir', 7 => 'Baramhat', 8 => 'Baramuda',
            9 => 'Bashans', 10 => 'Baouna', 11 => 'Abib', 12 => 'Mesra',
            13 => 'Epagomenae'
        );
        
        $months = ($lang === 'en') ? $months_en : $months_ar;
        
        return isset($months[$month]) ? $months[$month] : '';
    }
}

// Helper functions for backward compatibility with original script
if (!function_exists('katawp_convert_coptic_to_gregorian')) {
    function katawp_convert_coptic_to_gregorian($coptic_date) {
        return KataWP_Date_Converter::coptic_to_gregorian($coptic_date);
    }
}

if (!function_exists('katawp_get_current_dates')) {
    function katawp_get_current_dates() {
        return KataWP_Date_Converter::get_current_dates();
    }
}

if (!function_exists('katawp_is_coptic_date')) {
    function katawp_is_coptic_date($coptic_date) {
        return KataWP_Date_Converter::is_valid_coptic_date($coptic_date);
    }
}

?>
