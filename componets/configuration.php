<?php

/**
 * Konfiguracja systemu - Księgarnia ZSE
 */

// ====== KONFIGURACJA BAZY DANYCH ======
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');
define('MYSQL_DB', 'bookdb');

// ====== UPRAWNIENIA DOSTĘPU ======
define('SYSTEM_ACCESS', true);
define('LOGIN_ACCESS', true);

// ====== KONFIGURACJA OGÓLNA ======
define('LOCATION', 'http://localhost/zse/');
define('SENDER', 'sklep@zse.rzeszow.pl');
