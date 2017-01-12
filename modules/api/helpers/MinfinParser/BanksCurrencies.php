<?php

namespace app\modules\api\helpers\MinfinParser;

class BanksCurrencies extends MinfinParser {

    const PAGE = 'banks/';

    /**
     * receiving data from the page "courses in banks"
     * @return array
     */
    public function getCurrenciesInBanks(){
        $html = $this->curl(self::PAGE);
        $document = \phpQuery::newDocument($html);
        $table = $document->find('.mfm-table.mfcur-table-lg-banks.mfcur-table-lg');
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
        $evrg = $row->find('td:eq(1)');
        $nbu = $row->find('td:eq(2) > span');
        $currency_auction = $row->find('td:eq(3)');

        $evrg->find('*')->remove();
        $nbu->find('*')->remove();
        $currency_auction->find('*')->remove();

        $evrg = $this->inner_trim_spaces($evrg->text());
        $nbu = $this->inner_trim_spaces($nbu->text());
        $currency_auction = $this->inner_trim_spaces($currency_auction->text());

        $evrg = $this->formatNums($evrg);
        $currency_auction = $this->formatNums($currency_auction);

        return [
            'evrg' => $evrg,
            'nbu' => $nbu,
            'currency_auction' => $currency_auction,
        ];
    }

    private function formatNums($str){
        return preg_replace('/([\d.]+)\s([\d.]+)/', '$1 / $2', $str);
    }

}