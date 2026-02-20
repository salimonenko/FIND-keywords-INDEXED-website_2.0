<?php

error_reporting(E_ALL);

mb_internal_encoding("UTF-8");
$internal_enc = mb_internal_encoding();
mb_regex_encoding("utf-8");

if(!defined('flag_perfom_working') || (flag_perfom_working != '1')) {
    header('Content-type: text/html; charset=utf-8');
    die('Эту программу нельзя запускать непосредственно. Access forbidden.');
}

/*****************************    Ненужное (Начало)   ********
// 1. Задаваемые параметры/функции

//header('Content-type: text/html; charset=utf-8');

//$t0 = microtime(true);

// 2. Подключаемые модули
//require __DIR__ . '/../parametrs.php';

// 3. Получаем искомые слова для поиска (для тестирования правильности нахождения слов в начальных формах)
$str = file_get_contents((__DIR__ . '/test_words.txt'));
$str = preg_replace('|[^\sАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя]|', ' ', $str);
$str = preg_replace('|[\S]{0, 6}|', ' ', $str);

$str = 'делали  красивый';
$str_Arr = preg_split('|\s+|', $str, -1, PREG_SPLIT_NO_EMPTY);
*****************************    /Ненужное (Конец)   *********/


// 4. Массив всевозможных окончаний русск. яз.
$ru_aff_Arr = array_map(function ($elem){
                        return preg_split('|\s+|', $elem, -1, PREG_SPLIT_NO_EMPTY);
                        }, file($ru_aff_FILE_NAME));

// 5. Берем все слова русск. яз. из словаря (браузера Firefox 36)
$ALL_words = file_get_contents($ru_dic_FILE_NAME); // Все слова русск. яз. в начальной форме (в именит. падеже, неопред. форма глагола и т.п.)


// 6. Для каждого искомого слова из массива кл. слов запускаем процедуру поиска близких слов (с разными окончаниями)
/*$words_to_find_Arr = array();
for($j=0; $j < sizeof($str_Arr); $j++){
     $rez_Arr = check_WORD_in_DIC($ru_aff_Arr, $ALL_words, $internal_enc, $str_Arr[$j]);
     $words_to_find_Arr = array_merge($words_to_find_Arr, $rez_Arr);
}*/
//check_WORD_in_DIC($ru_aff_Arr, $ALL_words, $internal_enc, 'делали');
//  долю


/**********     ФУНКЦИИ PHP     **************************/

function check_WORD_in_DIC($ru_aff_Arr, $ALL_words, $internal_enc, $text){

set_time_limit(40); // С этого момента скрипт будет выполняться не более указанного количества секунд (каждая итерация цикла)


$word = mb_strtolower($text, $internal_enc);

$finded_Arr = array();
$max_len = 0;

// 1. Анализируем каждое окончание: содержится ли оно в конце слова?
for($i=0; $i < sizeof($ru_aff_Arr); $i++){

    if(isset($ru_aff_Arr[$i][3])){ // Если содержится, то добавляем это окончание в массив
        if(preg_match('|'. $ru_aff_Arr[$i][3]. '$|' , $word) != false){
            $finded_Arr[$i] = $ru_aff_Arr[$i];
            $finded_Arr[$i][5] = mb_strlen($ru_aff_Arr[$i][3], $internal_enc);
            if($finded_Arr[$i][5] > $max_len){
                $max_len = $finded_Arr[$i][5]; // Возможно, это не нужно +++
            }

        }elseif(isset($ru_aff_Arr[$i][4])){ // Если не содержится, тогда, может, есть окончание из следующего столбца?
            if(preg_match('|'. $ru_aff_Arr[$i][4]. '$|' , $word) != false){
                $finded_Arr[$i] = $ru_aff_Arr[$i];
                $finded_Arr[$i][5] = mb_strlen($ru_aff_Arr[$i][4], $internal_enc);
                if($finded_Arr[$i][5] > $max_len){
                    $max_len = $finded_Arr[$i][5]; // Возможно, это не нужно +++
                }

            }
        }

    }
}


/* ТАК ГОРАЗДО БЫСТРЕЕ, НО НЕКОТОРЫЕ СЛОВА НЕ ОБНАРУЖИВАЮТСЯ
$finded_Arr = array_filter($finded_Arr, function ($el) use ($max_len, $internal_enc){
    return $el[5] === $max_len; // Оставляем только такие окончания, которые имеют максимальную длину
});*/

$finded_Arr = array_values($finded_Arr);

$finded_Arr1 = array($word);

// 2. По массиву всех окончаний, к-рые могут содержаться в слове
for($i=0; $i < sizeof($finded_Arr); $i++){
    if($finded_Arr[$i][2] === '0'){
        $replacement = '';
    }else{
        $replacement = $finded_Arr[$i][2];
    }

    $tmp = preg_replace('|'. $finded_Arr[$i][3]. '$|', $replacement, $word); // Заменяем фактическое окончание на окончание начальной формы


    if(preg_match('|'. $finded_Arr[$i][4]. '$|' , $tmp) != false){
        $finded_Arr1[] = $tmp;

        if($finded_Arr[$i][2] === '0'){
            $finded_Arr1[] = $tmp. $finded_Arr[$i][3]; // Добавляем фактическое окончание
        }
    }
}
$finded_Arr1 = array_values(array_unique($finded_Arr1));



$word_DIC_suff_Arr = array();

// 3. Проверяем каждое из найденных слов в начальной форме: содержится ли оно в словаре ru.dic ?
for($i=0; $i < sizeof($finded_Arr1); $i++){

    if(preg_match('|(\s'. $finded_Arr1[$i]. '/?[^\sабвгдеёжзийклмнопрстуфхцчшщъыьэюя a-z]*?\s)|', $ALL_words, $matches) > 0){
        $word_DIC_suff_Arr[] = $matches[1];
    }
}
$word_DIC_suff_Arr = array_values($word_DIC_suff_Arr);

// 4. Теперь каждое слово в начальной форме нужно просклонять по разным окончаниям, в зависимости от суффикса из файла ru.dic
$words_to_find_Arr = array(); // Выходной массив слов для последующего поиска (с учетом разных окончаний)
/*   Там будет что-то вроде:
        Array
        (
            [0] => сумма
            [1] => суммы
            [2] => сумму
            [3] => суммой
            [4] => суммою
            [5] => сумме
            [6] => сумм
            [7] => суммами
            [8] => суммам
            [9] => суммах
        )
*/
// 5. По каждому слово-суффиксу (например: сумма/I )
    for($i=0; $i < sizeof($word_DIC_suff_Arr); $i++){

        $DIS_suf = trim(substr($word_DIC_suff_Arr[$i], strpos($word_DIC_suff_Arr[$i], '/') + 1)); // Суффикс БЕЗ слова из файла ru.dic
        $DIC_word = trim(substr($word_DIC_suff_Arr[$i], 0, strpos($word_DIC_suff_Arr[$i], '/'))); // Слово БЕЗ суффикса из файла ru.dic

// Вначале в этот массив добавляем само слово в начальной форме (именительный падеж существительного, неопределенная форма глагола и т.п.)
$words_to_find_Arr[] = $DIC_word;

        $suf_Arr = array_filter($ru_aff_Arr, function ($el) use ($DIS_suf) { // Для конкретного слова берем все строки из файла ru.aff, только с суффиксом $DIS_suf
            if(isset($el[1]) && sizeof($el) > 4){
// Если в совокупности суффиксов из файла ru.dic (например, BLMP) есть хотя бы один суффикс из файла ru.aff (например, B)
                return strpos($DIS_suf, $el[1]) !== false;
            }else{
                return null;
            }
        });
        $suf_Arr = array_values($suf_Arr);

        for($j=0; $j < sizeof($suf_Arr); $j++){ // По каждой строчке из файла ru.aff, содержащей суффикс $DIS_suf

            if(preg_match('|'. $suf_Arr[$j][4]. '$|', $DIC_word)){
                if($suf_Arr[$j][2] === '0'){
                    $removed = '';
                }else{
                    $removed = $suf_Arr[$j][2];
                }

                if($suf_Arr[$j][3] === '0'){
                    $replacement = '';
                }else{
                    $replacement = $suf_Arr[$j][3];
                }

                $tmp = preg_replace('|'. $removed. '$|', $replacement, $DIC_word);
                $words_to_find_Arr[] = $tmp;
            }
        }
    }

return array_unique($words_to_find_Arr);
}
