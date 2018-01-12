<?php

namespace Models;

class DataHandler
{
    //Данные, полученные из файла .csv
    private $data;

    //Массив, содержащий обменные курсы
    private $exchangeRate;

    //Конструктор 
    public function __construct($array) {
        $path = ROOT . DIRECTORY_SEPARATOR . 'Config'. DIRECTORY_SEPARATOR . 'ExchangeRates.php';
        $this->data = $array;
        $this->exchangeRate = require_once($path);
    }

    //Возвращает массив строк, отсортированных по шаблону
    public function searchLines() {
        $pattern = 'PAYMENT[0-6]{6}[A-Z]{2}';
		$patternDate = '01\/09\/2017';
        $result = array();
        foreach ($this->data as $line) {
            if (preg_match("/$pattern/", $line) && preg_match("/$patternDate/", $line))
                $result[] = trim($line);
        }
        return $result;
    }

    //Разбить массив строк на ячейки
    public function explodeLines($array) {
        $res = array();
        for ($i = 0; $i<count($array); $i++) {
            $res[$i] = explode(",", $array[$i]);
            $res[$i] = $this->combineArray($res[$i]);
        }
        return $res;
    }

    //Создание массива с ключами и значениями(хеш-таблица)
    private function combineArray($array) {
        $keys = ['Date', 'Narrative1', 'Narrative2', 'Narrative3', 'Narrative4', 'Narrative5', 'Type', 'Credit', 'Debit', 'Currency'];
        return array_combine($keys, $array);
    }

    //Возвращает массив сумм платежек раздельно по валюте
    public function countTotalSumCurrency($array, $deb='Debit', $cur='Currency') {
        $result = array();
        for ($i = 0; $i < count($array); $i++) {
            //var_dump($array[$i][$cur]); var_dump($array[$i][$deb]);
            switch ($array[$i][$cur]) {
                case 'EUR':
                    array_key_exists('EUR', $result) ? $result['EUR'] += (float)$array[$i][$deb] : $result['EUR'] = (float)$array[$i][$deb];
                    break;
                case 'GBP':
                    array_key_exists('GBP', $result) ? $result['GBP'] += (float)$array[$i][$deb] : $result['GBP'] = (float)$array[$i][$deb];
                    break;
                case 'CAD':
                    array_key_exists('CAD', $result) ? $result['CAD'] += (float)$array[$i][$deb] : $result['CAD'] = (float)$array[$i][$deb];
                    break;
                case 'USD':
                    array_key_exists('USD', $result) ? $result['USD'] += (float)$array[$i][$deb] : $result['USD'] = (float)$array[$i][$deb];
                    break;
            }
        }
        return $result;
    }

    //Возвращает общую полученную сумму платежек, конвертированных в различную валюту
    public function countTotalSum($array, $deb='Debit', $cur='Currency') {
        $result = $this->countTotalSumCurrency($array, $deb='Debit', $cur='Currency');
        $totalSum = 0.0; //USD
        foreach ($array as $payment) {
            switch ($payment[$cur]) {
                case 'EUR':
                    $totalSum += $this->exchangeRate['EUR']*$payment[$deb];
                    break;
                case 'GBP':
                    $totalSum += $this->exchangeRate['GBP']*$payment[$deb];
                    break;
                case 'CAD':
                    $totalSum += $this->exchangeRate['CAD']*$payment[$deb];
                    break;
                case 'USD':
                    $totalSum += $payment[$deb];
                    break;
            }
        }
        return [
            'EUR' => round($totalSum/$this->exchangeRate['EUR'], 2),
            'GBP' => round($totalSum/$this->exchangeRate['GBP'], 2),
            'CAD' => round($totalSum/$this->exchangeRate['CAD'], 2),
            'USD' => round($totalSum, 2),
        ];
    }
}