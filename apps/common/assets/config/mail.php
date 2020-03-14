<?php
/**
 * Настройки для отправки почты
 */
$config = array();

$config['accounts']['default']['host'] = 'localhost';
$config['accounts']['default']['port'] = 25;
$config['accounts']['default']['from'] = 'Opake Admin <noreply@opake.com>';

return $config;