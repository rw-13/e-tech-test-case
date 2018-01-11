<?php
/**
 * @author <may-be-intern>
 * @package test
 *
 */

class Bootstrap
{
	public static function main($argv=0)
	{
		define('ROOT', dirname(__FILE__));

		require_once ROOT . "/Components/Autoload.php";

		//Чтение из файла
		$obfFileM = new Components\FileManager();
		$obfFileM->loadFile('Report.csv');
		$data = $obfFileM->getListLinesFile();
		$obfFileM->closeFile();

		//Работа с данными
		$objDataH = new Models\DataHandler($data);
		//var_dump($objDataH);

		//Форматирование массива
		$resArr1 = $objDataH->searchLines();
		//var_dump($resArr1);
		$resArr1 = $objDataH->explodeLines($resArr1);
		//var_dump($resArr1);

		//Арифметические расчеты
		$resArr1 = $objDataH->countTotalSum($resArr1);
		var_dump($resArr1);

		// количество аргуметов переданных в консоли
		//echo "The number of array elements \$argv = " . $_SERVER['argc'] . "\n";
		// вывод массива аргументов переданных в консоли
		//print_r($_SERVER['argv']);
	}
}

Bootstrap::main();