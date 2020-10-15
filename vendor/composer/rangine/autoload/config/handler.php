<?php return array (
  'session' => 
  array (
    'file' => 'W7\\Core\\Session\\Handler\\FileHandler',
  ),
  'cache' => 
  array (
    'redis' => 'W7\\Core\\Cache\\Handler\\RedisHandler',
  ),
  'log' => 
  array (
    'stream' => 'W7\\Core\\Log\\Handler\\StreamHandler',
    'syslog' => 'W7\\Core\\Log\\Handler\\SyslogHandler',
    'errorlog' => 'W7\\Core\\Log\\Handler\\ErrorlogHandler',
    'daily' => 'W7\\Core\\Log\\Handler\\DailyHandler',
  ),
  'view' => 
  array (
    'twig' => 'W7\\Core\\View\\Handler\\TwigHandler',
  ),
);