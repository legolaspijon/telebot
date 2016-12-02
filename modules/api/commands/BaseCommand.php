<?php

namespace app\modules\api\commands;
use app\modules\api\models\Users;

abstract class BaseCommand {

    /**
     * @var $args
     * */
    public $answer;

    /**
     * @var $update array of response
     * */
    public $update;

    /**
     * @var $user Users
     * */
    public $user;

    public function __construct($update, $user, $answer = null) {
        $this->answer = $answer;
        $this->update = $update;
        $this->user = $user;
    }

    abstract public function execute();
}