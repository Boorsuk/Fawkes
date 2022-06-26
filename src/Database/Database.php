<?php

declare(strict_types = 1);

namespace Fawkes\Database;

/**
 * @mixin PDO
 * @package Fawkes\Database
 */
class Database
{
    private \PDO $pdo;

    public function __construct(array $config){
        $source   = $config['datasource'] ?? null;
        $host     = $config['host'] ?? null;
        $dbname   = $config['dbname'] ?? null;
        $login    = $config['login'] ?? null;
        $password = $config['password'] ?? null;

        try {
            $this->pdo = new \PDO("${source}:host=${host};dbname=${dbname}", $login, $password);
        } catch (\PDOException $e) {
            /** remove sensitive data from exception */
            throw new \PDOException($e->getMessage(), $e->getCode());
        }
    }

    public function __call($name, $arguments){
        return call_user_func_array([$this->pdo, $name], $arguments);    
    }
}