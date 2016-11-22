<?php

namespace app\modules\api\commands;

abstract class BaseCommand {

    public $update;

    public function __construct($update)
    {
        $this->update = $update;
    }

    abstract public function execute();
    
}