<?php

namespace app\modules\api\helpers\MinfinParser;

class Cards extends MinfinParser {

    const PAGE = 'cards/';

    /**
     * receiving data from the page "cards Visa/MasterCard"
     * @return array
     */
    public function getCards(){
        $html = $this->curl(self::PAGE);
        $document = \phpQuery::newDocument($html);
        $table = $document->find('.mfm-table.mfcur-table-lg-cards.mfcur-table-lg');
        $row_usd = $table->find('tr:eq(1)');
        $row_eur = $table->find('tr:eq(2)');
        $row_rub = $table->find('tr:eq(3)');


        return [
            'usd' => $this->parse_row($row_usd),
            'eur' => $this->parse_row($row_eur),
            'rub' => $this->parse_row($row_rub)
        ];
    }

    /**
     * @param \phpQueryObject $row
     * @return array
     */
    private function parse_row(\phpQueryObject $row){
        $evrgCard = $row->find('td:eq(1)');
        $courseVisa = $row->find('td:eq(2)');
        $courseMasterCard = $row->find('td:eq(3)');
        $evrgCard->find('*')->remove();
        $courseVisa->find('*')->remove();
        $courseMasterCard->find('*')->remove();


        $evrgCard =  $this->inner_trim_spaces($evrgCard->text());
        $courseVisa = $this->inner_trim_spaces($courseVisa->text());
        $courseMasterCard = $this->inner_trim_spaces($courseMasterCard->text());

        $evrgCard =  $this->formatNums($evrgCard);
        $courseVisa = $this->formatNums($courseVisa);
        $courseMasterCard = $this->formatNums($courseMasterCard);

        return [
            'evrgCard' => $evrgCard,
            'courseVisa' => $courseVisa,
            'courseMasterCard' => $courseMasterCard,
        ];
    }

    private function formatNums($str){
        return preg_replace('/([\d.]+)\s([\d.]+)/', '$1 / $2', $str);
    }
}