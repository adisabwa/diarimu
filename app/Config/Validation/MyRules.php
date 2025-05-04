<?php
namespace Config\Validation;

class MyRules
{
    public function __construct()
    {
        helper('function');
    }

    public function double_greater_and_equal_to(string $str, string $field, array $data): bool
    {
        $compareField = $data[$field] ?? null;
        // var_dump($str, $compareField);
        
        $str = explode('-', $str);
        $compareField = explode('-', $compareField);
        
        if (count($str) != 2 || count($compareField) != 2) {
            return false;
        }

        if (!is_numeric($str[0]) || !is_numeric($str[1]) || !is_numeric($compareField[0]) || !is_numeric($compareField[1])) {
            return false;
        }
        
        if ($str[0] > $compareField[0])
            return true;
        else if ($str[0] == $compareField[0])
            return $str[1] >= $compareField[1];
        else
            return false;
    }
}