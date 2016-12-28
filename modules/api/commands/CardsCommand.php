<?php

namespace app\modules\api\commands;
use \app\modules\api\helpers\Cards;

class CardsCommand extends BaseCommand {

    public function execute()
    {
        $cards = new Cards();
        $cards_info = $cards->getCards();
        $text = "\n<b>Visa/MasterCard Курс к гривне (покупка / продажа)</b>";
        $text .= "\n\n<b>Доллар</b>";
        $text .= $this->text($cards_info['usd']);
        $text .= "\n\n<b>Евро</b>";
        $text .= $this->text($cards_info['eur']);
        $text .= "\n\n<b>Рубль</b>";
        $text .= $this->text($cards_info['rub']);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }

    private function text($currency){
        $text = "\nСреднекарточный: {$currency['evrgCard']}";
        $text .= "\nКурс Visa: {$currency['courseVisa']}";
        $text .= "\nКурс MasterCard: {$currency['courseMasterCard']}";
        return $text;
    }
}