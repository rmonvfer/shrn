<?php


class ErrorManager
{
    public static function display_field_error($field) : void
    {
        $output = '<p class="form-error_hidden"></p>';
        if (!empty($field))
        {
            $output = '<p class="form-error">'.$field.'</p>';
        }
        echo $output;
    }
}