<?php

namespace app\modules\api\helpers\MinfinParser;

class Interbank extends MinfinParser
{
    const PAGE = 'mb/';

    /**
     * receiving data from the page "Interbank"
     */
    public function getMB(){
        $html = $this->curl(self::PAGE);
        $document = \phpQuery::newDocument($html);
        $table = $document->find('.mb-table-currency');
        $buy = $table->find('tr:eq(1)');
        $cell = $table->find('tr:eq(2)');

        return [
            'buy' => $this->parseRow($buy),
            'cell' => $this->parseRow($cell),
        ];
    }

    /**
     * @param \phpQueryObject $row
     * @return array
     */
    private function parseRow(\phpQueryObject $row){
        $usd = $row->find('td:eq(1)');
        $eur = $row->find('td:eq(2)');
        $rub = $row->find('td:eq(3)');

        $usd->find('*')->remove();
        $eur->find('*')->remove();
        $rub->find('*')->remove();

        return [
            'usd' => $this->inner_trim_spaces($usd->text()),
            'eur' => $this->inner_trim_spaces($eur->text()),
            'rub' => $this->inner_trim_spaces($rub->text())
        ];
    }
}