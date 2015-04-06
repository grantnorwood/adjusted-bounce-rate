<?php

namespace gkn_abr;

/**
 * Class UserMessage
 */
class UserMessage {

    public $type = null;
    public $message = null;




    public function __construct($message = null, $type = null) {

        $this->type = $type;
        $this->message = $message;

    }

}