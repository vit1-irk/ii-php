ii-php
---------------
ii - это русская фидообразная сеть для обмена сообщениями, подробно узнать о ней вы можете на сайте [http://ii.51t.ru](http://ii.51t.ru).  
В этом репозитории находится простенькая php реализация "серверной" части ii. Для начала работы рядом со скриптами надо создать каталоги *echo* и *msg*. В файле *ii-functions.php* есть функция *msg_to_ii*, вызывая которую на своём сайте, вы можете писать сообщения в ii.

Выглядеть это будет примерно так:  
```php
include("ii-functions.php");
msg\_to\_ii($topicid."2014",$message,$usersent,"myforum, 1",time(),$userget,$subject,"");
//$usersent - пользователь, отправивший сообщение, а $userget - его получивший.
```
Синхронизация
----------------
Данная php нода поддерживает push-синхронизацию (когда другой узел закачивает сообщения на данный), а также fetch-синхронизацию (когда данный узел скачивает сообщения у другого) через скрипт webfetch.php, который сначала надо сконфигурировать, а потом настроить его запуск в cron.
