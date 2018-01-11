<?php

namespace Components;

class FileManager
{
    private $file;

    //Открывает файл
    public function loadFile($nameFile) {
        $this->file = fopen($nameFile, 'r') or die("Ошибка загрузки файла!");
    }

    //Закрывает файл
    public function closeFile() {
        fclose($this->file);
    }

    //Возвращает содержимое файла как массив ячеек, разбитых по разделителю
    public function getListCellsFile() {
        $dataFile = array();
        for ($i = 0; $data = fgetcsv($this->file, 0, ","); $i++) {
            $num = count($data);
            for ($c=0; $c < $num; $c++)
                $dataFile[$i][$c] = trim($data[$c]);
        }
        return $dataFile;
    }

    //Возвращает содержимое файла как массив строк
    public function getListLinesFile() {
        $dataFile = array();
        for ($i = 0; $data = fgets($this->file); $i++)
            $dataFile[$i] = trim($data);
        return $dataFile;
    }
}