<?php

namespace gkn_abr;

/**
 * Class Response
 */
class AjaxResponse {

    public $data = null;
    public $errors = array();

    public function __construct($data = null, $errors = array()) {

        $this->data = $data;
        $this->errors = $errors;

    }

}