# PHP-Language

### Настройка для работы с несколькими языками без потери ключей.
> Этот пример позволяет вам использовать несколько языков без потери ключей, при отсутствии одного или нескольких ключей они будут взяты из языкового пакета по умолчанию.
> 
> Все языковые пакеты должны быть по пути: ```корень```/assets/lang/```название```.json - все это нужно, чтобы ничего не изменять в коде. Путь можно изменить в переменной ```$path_main_lang```

```php
<?php
/* HINT^ - Языковые настройки */
/* HINT^ - Языковые настройки */
/* HINT^ - Языковые настройки */
$default_lang = "en";
$default_lang_tag = "en-US";

/* HINT^ - Короткое языковое значение (ru, by, en...) */
/* HINT^ - Изменять при изменении языка пользователем */
$languageID = substr(($_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? $default_lang), 0, 2);

/* HINT^ - Стандартное языковое значение (ru-RU, be-BY, en-US...) */
/* HINT^ - Изменять при изменении языка пользователем */
$languageTAG = substr(($_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? $default_lang_tag), 0, 5);

/* HINT^ - Место, где лежать все языки */
$path_main_lang = $_SERVER["DOCUMENT_ROOT"] . "/assets/lang";
// $path_main_lang = dirname(__FILE__);

/* HINT^ - Загрузка стандартного языкового пакета в JSON */
$content_default = file_get_contents("$path_main_lang/$default_lang.json");

/* HINT^ - Загрузка языкового пакета в JSON из настроек пользователя */
$content_setting = $content_default;
$content_user_lang = trim(str_replace("/", "", substr(strval($_COOKIE["lang"] ?? $default_lang), 0, 2)));
if (file_exists("$path_main_lang/$content_user_lang.json")) {
    $content_setting = file_get_contents("$path_main_lang/$content_user_lang.json");
}

/* HINT^ - Преобразование языкового пакета в список */
$string_default = json_decode($content_default, true);
$string_setting = json_decode($content_setting, true);

/* HINT^ - Заменяем повторяющиеся ключи */
$string = array_merge($string_default, $string_setting);

/* HINT^ - Изменять при изменении языка пользователем */
$language_tag = strval($string["language_tag"] ?? ($languageTAG ?? $default_lang_tag)); // Для атрибута lang=""

/* HINT^ - Возвращаем JSON */
$content = json_encode($string); // Для JS списка

/**
 * Функция выводит необходимый текст по его ключу, если текста под этим ключом нет, будет выведен этот ключ
 * @param string $key
 * @param bool $html (необязательно) обрабатывает html теги
 * @param array $replace заменяет %1s, %2s, %3s и т.д. на ваш текст
 * @return string
 */
function str_get_string(string $key = "", bool $html = false, ...$replace):string {
    global $string;
    $str = array_key_exists($key, $string) ? $string[$key] : $key;
    if (sizeof($replace) > 0)
        for($i = 0; $i < sizeof($replace); $i++) $str = str_replace("%" . ($i + 1) . "s", $replace[$i], $str);

    return $html ? $str : htmlspecialchars($str);
}
```


```php
<?php
global $language_tag, $string;

/* HINT^ - Подключаем файл с настройками языка */
include_once $_SERVER["DOCUMENT_ROOT"] . "/assets/prefs/lang.php";
?>
<!DOCTYPE html>
<html lang="<?= $language_tag ?? "en-US" ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= str_get_string("project_name") ?></title>
    <!-- ИЛИ (рекомендуется пример выше) -->
    <title><?= $string["project_name"] ?></title>
    <!-- Результат: PHP-Language/PHP-Языковой пакет -->

    <script type="text/javascript">
        const stringOBJ = <?= $content ?>;
    </script>
</head>
<body>
    <h1 id="hello_world"></h1>
    
    <script>
        document.querySelector("#hello_world").innerText = str_get_string("project_name"); // Результат: PHP-Language/PHP-Языковой пакет
    </script>
</body>
</html>
```

### Пример языкового пакета en.json
```json
{
    "language_tag":"en-US",
    "project_name":"PHP-Language"
}
```

### Пример языкового пакета ru.json
```json
{
    "project_name":"PHP-Языковой пакет"
}
```
В русском примере отсутвует ключ "language_tag", он будет взят из языкового пакета по умолчанию en.json
