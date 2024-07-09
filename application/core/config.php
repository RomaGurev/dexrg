<?
/*
Класс конфигурации
> содержит методы для загрузки файла конфигурации
> содержит метод получения значения конфига по ключу
*/
$path = $_SERVER['DOCUMENT_ROOT'] . "/config.json";

if(file_exists($path)) 
	Config::Load($path);
else
	echo "Отсутствует или поврежден файл конфигурации.";

class Config
{

    private static $values = array();

    //Метод для загрузки файла конфигурации
    public static function Load($filename)
    {
        static::$values = json_decode(file_get_contents($filename), true);
    }

    //Метод получения значения по ключу
    public static function getValue($key)
    {
        if (isset(static::$values[$key]))
            return static::$values[$key];
    }
}