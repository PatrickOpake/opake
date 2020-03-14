<?php
/**
 * Настройки для отправки почты
 */
$config = array();

$config['accounts']['default']['host'] = '172.17.0.1';
$config['accounts']['default']['port'] = 25;
$config['accounts']['default']['from'] = 'Opake Admin <noreply@opake.com>';

return $config;