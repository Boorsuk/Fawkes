<?php

declare(strict_types = 1);

namespace Fawkes;

class Config
{
    private const ENV_DB_NAME     = 'dbname'; 
    private const ENV_DB_HOST     = 'host'; 
    private const ENV_DB_LOGIN    = 'login'; 
    private const ENV_DB_PASSWORD = 'password'; 
    private const ENV_DB_SOURCE   = 'datasource'; 

    private array $configs = [];

    public function __construct(array $env){
        /** DB SECTION */
        $this->configs['db'] = [
            self::ENV_DB_NAME     => $env[self::ENV_DB_NAME] ?? null,
            self::ENV_DB_HOST     => $env[self::ENV_DB_HOST] ?? null,
            self::ENV_DB_LOGIN    => $env[self::ENV_DB_LOGIN] ?? null,
            self::ENV_DB_PASSWORD => $env[self::ENV_DB_PASSWORD] ?? null,
            self::ENV_DB_SOURCE   => $env[self::ENV_DB_SOURCE] ?? null,
        ];
    }

    public function getDatabaseConfig() : array{
        return $this->configs['db'];
    }
}