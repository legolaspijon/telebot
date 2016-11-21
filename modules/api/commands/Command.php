<?php

namespace app\modules\api\commands;

class Command {

    public $update;
    
    public function __construct($update)
    {
        $this->update = $update;
    }
}