<?php

if (! function_exists('getValidatorErrorMessage')) {
  function getValidatorErrorMessage($validator) {
    $errmsg = '';
    foreach (json_decode($validator->messages(), true) as $key => $value) {
      $errmsg .= implode(', ', $value) . ', ';
    }
    return substr($errmsg, 0, -2);
  }
}

if (! function_exists('getValidatorMessages')) {
  function getValidatorMessages() {
    return [
      '*.required' => 'The :attribute is required',
      '*.max' => 'The :attribute is over the limit'
    ];
  }
}