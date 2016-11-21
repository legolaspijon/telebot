<?php

namespace app\modules\api\commands;

class StartCommand extends Command{

    public function execute(){
        var_dump((array)$this->update);exit;
    }
}