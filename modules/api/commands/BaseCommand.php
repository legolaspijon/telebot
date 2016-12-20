<?php

namespace app\modules\api\commands;
use app\modules\api\controllers\TelegramController;
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
     * @var $bot TelegramController
     * */
    public $bot;

    public function __construct($update, Users $user, $answer = null, TelegramController $bot) {
        $this->answer = $answer;
        $this->update = $update;
        $this->user = $user;
        $this->bot = $bot;
    }

    abstract public function execute();
}