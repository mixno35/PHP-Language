# PHP-Language

### Настройка для работы с несколькими языками без потери ключей.

```php
<?php
    /* HINT^ - Языковые настройки */
    /* HINT^ - Языковые настройки */
    /* HINT^ - Языковые настройки */

    /* HINT^ - Короткое языковое значение (ru, by, en...) */
    /* HINT^ - Изменять при изменении языка пользователем */
    $languageID = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

    /* HINT^ - Стандартное языковое значение (ru-RU, be-BY, en-US...) */
    /* HINT^ - Изменять при изменении языка пользователем */
    $languageTAG = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);

    /* HINT^ - Загрузка стандартного языкового пакета в JSON */
    $content_default = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/assets/lang/en.json', false);

    /* HINT^ - Загрузка языкового пакета в JSON из настроек пользователя */
    $content_setting = $content_default;
    $content_user_lang = trim(str_replace('/', '', strval(substr($_COOKIE['lang'], 0, 2))));
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/lang/' . $content_user_lang . '.json')) {
        $content_setting = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/assets/lang/' . $content_user_lang . '.json', false);
    }

    /* HINT^ - Преобразование языкового пакета в список */
    $string_default = json_decode($content_default, true);
    $string_setting = json_decode($content_setting, true);

    /* HINT^ - Заменяем повторяющиеся ключи */
    $string = array_merge($string_default, $string_setting);

    /* HINT^ - Изменять при изменении языка пользователем */
    $languageTAG = strval($string['language_tag'] ?? 'en-US'); // Для атрибута lang=""

    /* HINT^ - Возвращаем JSON */
    $content = json_encode($string); // Для JS списка
?>
```


```php
<?php
    /* HINT^ - Загрузка стандартных настроек */
    include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/prefs/lang.php';
?>
<!DOCTYPE html>
<html lang="<? echo strval($languageTAG ?? 'en-US'); ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><? echo $string['web_name']; ?></title>
    <!-- PHP-Language -->

    <script type="text/javascript">
        var stringOBJ = JSON.parse(<? echo json_encode($content ?? '{}'); ?>);
    </script>
</head>
<body>
    
</body>
</html>
```

### Примем языкового пакета en.json
```json
{
    "language_tag":"en-US",
    "project_name":"PHP-Language"
}
```

### Примем языкового пакета ru.json
```json
{
    "project_name":"PHP-Языковой пакет"
}
```
В русском примере отсутвует ключ "language_tag", он будет взят из языкового пакета по умолчания en.json
