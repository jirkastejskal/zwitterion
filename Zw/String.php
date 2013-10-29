<?php

class ZwString
{
    protected static $_translationTable = array(

        "á"=>"a", "ä"=> "a", "č"=>"c", "ď"=>"d", "é"=>"e", "ě"=>"e",
        "ë"=>"e", "í"=>"i", "ï"=>"i", "ň"=>"n", "ó"=>"o", "ö"=>"o", "ř"=>"r",
        "š"=>"s", "ť"=>"t", "ú"=>"u", "ů"=>"u", "ü"=>"u", "ý"=>"y", "ÿ"=>"y",
        "ž"=>"z", "Á"=>"a", "Ä"=>"a", "Č"=>"c", "Ď"=>"d", "É"=>"e", "Ě"=>"e",
        "Ë"=>"e", "Í"=>"i", "Ï"=>"i", "Ň"=>"n", "Ó"=>"o", "Ö"=>"o", "Ř"=>"r",
        "Š"=>"s","Ť"=>"t", "Ú"=>"u", "Ů"=>"u", "Ü"=>"u", "Ý"=>"y", "Ÿ"=>"y",
        "Ž"=>"z"
    );

    protected static $_htmlspecialchars = array(
//        "\"" => "",
        "'" => "´",
        ">" => "",
        "<" => ""
    );

    /**
     * @param string $value
     * @return string
     */
    static public function to_lower($value)
    {
        $value = strtr($value, self::$_translationTable);
        $value = strtolower($value);
        return trim($value);
    }

    /**
     * @param string $value
     * @return string
     */
    static public function seo_url($value)
    {
        $value = self::to_lower($value);
        $value = strip_tags($value);
        $value = trim($value);
        preg_match_all('/[a-zA-Z0-9]+/', $value, $nt);
        return implode('-', $nt[0]);
    }

    /**
     * @param string $value
     * @return string
     */
    static public function safe_string($value)
    {
        if (is_string($value) == false)
            return $value;
        $value = strip_tags($value);
        $value = strtr($value,self::$_htmlspecialchars);
        return trim($value);
    }

    /**
     * @param array $arr
     */
    static public function safe_string_array(&$arr)
    {
        foreach($arr as $key => $value) {
            $arr[$key] = self::safe_string($value);
        }
    }
}