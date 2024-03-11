<?

$dir = $_SERVER['DOCUMENT_ROOT'] . "/config.json";

if(file_exists($dir)) 
	Config::Load($dir);
else
	echo "Отсутствует или поврежден файл конфигурации.";

class Config
{

    private static $values = array();

    public static function Load($filename)
    {
        static::$values = json_decode(file_get_contents($filename), true);
    }

    public static function getValue($key)
    {
        if (isset(static::$values[$key]))
            return static::$values[$key];
    }

}

?>