Здравствуйте, на сервисе <?=CONFIG['NAME']?> была активирована функция сборса пароля.<br>
Введите код подтверждения на сайте и вам будет отправлен новый пароль.<br>
<p>Код подтверждения E-mail: <b><?=$_SESSION['confirm']['recode']?></b></p>
Страница подтверждения: https://<?=CONFIG['DOMAIN']?>/user/confirmRecovery/ <br>
Если вы не запускали функцию сборса пароля на сайте <?=CONFIG['DOMAIN']?> свяжитесь, пожалуйста, с администрацией.<br>