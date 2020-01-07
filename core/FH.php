<?php
namespace Core;
use Core\Session;

class FH {

    public static function inputBlock($type, $label, $name, $value='', $inputAttrs=[], $divAttrs=[]) {
        $divString = self::stringifyAttrs($divAttrs);
        $inputString = self::stringifyAttrs($inputAttrs);
        $html = '<div'.$divString.'>';
        $html .= '<label for="'.$name.'">'.$label.'</label>';
        $html .= '<input type="'.$type.'" id="'.$name.'" name="'.$name.'" value="'.$value.'"'.$inputString.' />';
        $html .= '</div>';
        return $html;
        //<?= inputBlock('text', 'Favourite colour', 'favorite_colour', 'red', ['class'=>'form-control'], ['class'=>'form-group']);
    }
    
    public static function submitTag($buttonText, $inputAttrs=[]) { //attributes are in the array
        $inputString = self::stringifyAttrs($inputAttrs);
        $html = '<input type="submit" value="'.$buttonText.'"'.$inputString.' />';
        return $html;
        //<?= submitTag("Save", ['class'=>'btn btn-primary']);
    }
    
    public static function submitBlock($buttonText, $inputAttrs=[], $divAttrs=[]) {
        $divString = self::stringifyAttrs($divAttrs);
        $inputString = self::stringifyAttrs($inputAttrs);
        $html = '<div'.$divString.'>';
        $html .= '<input type="submit" value="'.$buttonText.'"'.$inputString.' />';
        $html .= '</div>';
        return $html;
        //<?= submitBlock("Save", ['class'=>'btn btn-primary'], ['class' => 'text-right']);
    }

    public static function checkboxBlock($label, $name, $checked=false, $inputAttrs=[], $divAttrs=[]) {
        $divString = self::stringifyAttrs($divAttrs);
        $inputString = self::stringifyAttrs($inputAttrs);
        $checkString = ($checked) ? ' checked="checked"' : '';
        $html = '<div'.$divString.'>';
        $html .= '<label for="'.$name.'"><input type="checkbox" id="'.$name.'" name="'.$name.'" value="on"'.$checkString.$inputString.' />'.$label.'</label>';
        $html .= '</div>';
        return $html;
    }
    
    //make strings out of arrays
    public static function stringifyAttrs($attrs) {
        $string = '';
        foreach($attrs as $key => $val) {
            $string .= ' ' . $key . '="' . $val . '"';
        }
        return $string;
    }

    public static function generateToken() {
        $token = base64_encode(openssl_random_pseudo_bytes(32)); //generate random string
        Session::set('csrf_token', $token);
        return $token; //for form field
    }

    public static function checkToken($token) {
        return (Session::exists('csrf_token') && Session::get('csrf_token') == $token);
    }

    public static function csrfInput() {
        //has to be added in every form
        return '<input type="hidden" name="csrf_token" id="csrf_token" value="'.self::generateToken().'" />';
    }

      //sanitize user input
    public static function sanitize($dirty) {
        return htmlentities($dirty, ENT_QUOTES, 'UTF-8');
    }

    public static function posted_values($post) {
        $clean_ary = [];
        foreach($post as $key => $value) {
            $clean_ary[$key] = self::sanitize($value);
        }
        return $clean_ary;
    }

    public static function displayErrors($errors) {
        $hasErrors = (!empty($errors)) ? ' has-errors' : '';
         $html = '<div class="form-errors"><ul class="bg-danger'.$hasErrors.'">';
         foreach($errors as $field => $error) {
                 $html .= '<li class="text-light">'.$error.'</li>';
                 $html .=  '<script>jQuery("document").ready(function(){jQuery("#'.$field.'").addClass("is-invalid")});</script>';
         }
         $html .= '</ul></div>';
         return $html;
     }

}