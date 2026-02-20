# FIND-keywords-INDEXED-website_2.0

This is an improved version of the program for searching files on a website that contain a set of search words (possibly combined into a logical expression). For example, the following logical search expression is allowed:

word1 && (word2 || word3)

The program is launched from the file /INDEXER_files_2.0/file_INDEXER.php

First, the NAMES of all files (not prohibited from indexing) are indexed. This creates a file called files.txt.
Then, the CONTENTS of each file contained in files.txt are indexed. For a large website, this is a rather lengthy process. The result is a "metaphones" directory, containing numerous subdirectories containing "1.txt" files containing indexes of individual words from the site's file contents.
When using the fuzzy search option, words with different endings will also be searched. For example, for the word "work," a fuzzy search will search for the words "works," "work," "work," and so on. If at least one of these words is found in the search result, the search will be considered successful. In other words, a regular search will only find the word "work." A fuzzy search will match the logical expression:

(work || work || work || work || ...)

Fuzzy search works ONLY FOR THE RUSSIAN LANGUAGE.

Это - улучшенная версия программы поиска файлов на сайте, содержащих совокупность искомых слов (быть может, объединенных в логическое выражение). Например, допускается такое поисковое логическое выражение:

слово1 && (слово2 || слово3)

Запуск программы производится черед файл /INDEXER_files_2.0/file_INDEXER.php

Вначале следует проиндексировать ИМЕНА всех файлов (незапрещенных к индексированию). Образуется файл files.txt. 
Затем следует проиндексировать СОДЕРЖИМОЕ каждого из файлов, содержащихся в files.txt. Для большого сайта это - довольно долгий процесс. В результате образуется каталог metaphones, а там - множество вложенных каталогов, в них - файлы 1.txt, содержащие индексы отдельных слов из содержимого файлов сайта. 
При использовании опции нечеткого поиска будут искаться также слова с разными окончаниями. Например, для слова "работа" при нечетком поиске будут искаться слова "работы", "работой", "работе" и т.д. Если в результате поиска хотя бы одно из этих будет найдено, поиск будет считаться удачным. Иными словами, при обычном поиске будет найдено только слово "работа". При нечетком поиске будет найдено соответствие логическому выражению: 

(работа || работ || работой || работе || ...)

Нечеткий поиск работает ТОЛЬКО ДЛЯ РУССКОГО ЯЗЫКА.
