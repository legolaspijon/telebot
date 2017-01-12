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

    /**
     * @var $bot
     * */
    public $bot;

    public function __construct(Users $user, $answer = null, $bot) {
        $this->answer = $answer;
        $this->user = $user;
        $this->bot = $bot;
    }

    abstract public function execute();
}