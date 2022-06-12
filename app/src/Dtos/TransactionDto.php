<?php

declare(strict_types = 1);

namespace Fawkes\Dtos;

use Fawkes\Exceptions\FileNotFoundException;

class TransactionDto
{
    public string $date;
    public ?string $check;
    public string $description;
    public string $amount;

    public function __construct(string $date, string $description, string $amount, string $check = null){
        $this->date = $date;
        $this->check = $check;
        $this->description = $description;
        $this->amount = $amount;
    }

    public function fetchAsArray(?callable $mapper = null) : array{
        if(!$mapper){
            return $mapper($this);
        }

        return [
            'date'        => $this->date,
            'check'       => $this->check,
            'description' => $this->description,
            'amount'      => $this->amount
        ];
    }

    public static function createFromFile(string $path, array|callable $mapper = null) : array{
        if(!file_exists($path)){
            throw new FileNotFoundException('Transaction File not exists: '.$path);
        }

        $file = fopen($path, 'r');
        fgetcsv($file); // skip headers

        $transactions = [];
        try {
            while($line = fgetcsv($file)){
                [$date, $check, $description, $amount] = $line;
    
                if(!strlen($check)){
                    $check = null;
                }

                $transaction = new static($date, $description, $amount, $check);

                if($mapper && is_callable($mapper)){
                    $transaction = $mapper($transaction);    
                }

                if($mapper && is_array($mapper)){
                    [$class, $method] = $mapper;
                    $transaction = call_user_func_array([$class, $method], [$transaction]);
                }

                $transactions[] = $transaction;
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        } finally {
            fclose($file);
        }

        return $transactions;
    }
}