ii-php
---------------
ii - это русская фидообразная сеть для обмена сообщениями, подробно узнать о ней вы можете на сайте [http://ii.51t.ru](http://ii.51t.ru).  
В этом репозитории находится простенькая php реализация "серверной" части ii. В файле *ii-functions.php* есть функция *msg_to_ii*, вызывая которую на своём сайте, вы можете писать сообщения в ii.

Выглядеть это будет примерно так:  
<code>
include("ii-functions.php");
msg\_to\_ii($topicid."2014",$message,$usersent,"myforum, 1",time(),$userget,$subject,"");
//$usersent - пользователь, отправивший сообщение, а $userget - его получивший.
</code>