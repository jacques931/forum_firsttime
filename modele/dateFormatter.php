<?php
class DateFormatter
{
    private static function formatDate($date, $pattern)
    {
        $date = $date->toDateTime();
        $formatter = new IntlDateFormatter(
            'fr_FR', 
            IntlDateFormatter::FULL, 
            IntlDateFormatter::SHORT
        );
        $formatter->setPattern($pattern);

        return $formatter->format($date);
    }

    // Retourne la différence entre une date donnée et aujourd'hui
    public static function getDateDifference($date)
    {
        $date = $date->toDateTime();
        $now = new DateTime();

        $interval = $date->diff($now);

        if ($interval->y > 0) {
            return "il y a " . $interval->y . " années";
        } elseif ($interval->m > 0) {
            return "il y a " . $interval->m . " mois";
        } elseif ($interval->d > 0) {
            return "il y a " . $interval->d . " jours";
        } elseif ($interval->h > 0) {
            return "il y a " . $interval->h . " heures";
        } elseif ($interval->i > 0) {
            return "il y a " . $interval->i . " minutes";
        } else {
            return "à l'instant";
        }
    }

    public static function formatToDayMonthYear($date)
    {
        return self::formatDate($date, "d MMMM yyyy");
    }
}

?>