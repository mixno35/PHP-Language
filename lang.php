<?php
/* HINT^ - Языковые настройки */
/* HINT^ - Языковые настройки */
/* HINT^ - Языковые настройки */
const lang_default_code = "en"; // Язык по умолчанию
const lang_default_tag = "en-US"; // Тег по умолчанию

$lang_get = trim($_GET["lang"] ?? "");
$lang_id = substr(($_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? lang_default_code), 0, 2); // Язык устройства пользователя
$lang_tag = substr(($_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? lang_default_tag), 0, 5); // Тег устройства пользователя
$lang_setting = trim(str_replace("/", "", substr(strval($_COOKIE["lang"] ?? $lang_id), 0, 2))); // Язык исходя из настроек cookie "lang"
$lang_setting = strlen($lang_get) >= 2 ? substr($lang_get, 0, 2) : $lang_setting; // Если есть атрибут "lang" в GET запросе, игнорируем параметр в cookies 

$path_main_lang = dirname(__DIR__) . "/assets/lang";

$content_default = file_get_contents("$path_main_lang/" . lang_default_code . ".json"); // Загружаем язык по умолчанию
$content_setting = $content_default;

file_exists("$path_main_lang/$lang_setting.json") && $content_setting = file_get_contents("$path_main_lang/$lang_setting.json"); // Загружаем язык исходя из настроек

$string_default = parse_json_decode($content_default, true);
$string_setting = parse_json_decode($content_setting, true);

$string = array_merge($string_default, $string_setting);

$language_tag = strval(array_key_exists("language_tag", $string) ? $string["language_tag"] : lang_default_tag); // Получаем тег, чтобы использовать в атрибуте lang=""

$content_lang = json_encode($string);

/**
 * Функция выводит необходимый текст по его ключу, если текста под этим ключом нет, будет выведен этот ключ
 * @param string $key если строка с таким ключом существует, она будет использована; в противном случае будет использован сам ключ.
 * @param bool $html (необязательно) обрабатывает html теги
 * @param array $replace принимает несколько значений, нужно для замены %1s, %2s и т.д...
 * @return string
 */
function str_lang_string(string $key, bool $html = false, ...$replace): string {
    global $string;
    $str = array_key_exists($key, $string) ? $string[$key] : $key;
    $str = sprintf($str, ...$replace);

    return $html ? $str : htmlspecialchars(trim($str));
}

/**
 * Функция помогает избежать ошибок в json файле возникших из-за лишней запятой
 */
function parse_json_decode(string $json, ?bool $associative = null, int $depth = 512, int $flags = 0): mixed {
    $json = preg_replace("/,\s*([]}])/", "$1", $json);

    $decodedData = json_decode($json, $associative, $depth, $flags);

    if ($decodedData === null && json_last_error() !== JSON_ERROR_NONE) return json_decode($json, $associative, $depth, $flags);

    return $decodedData;
}
