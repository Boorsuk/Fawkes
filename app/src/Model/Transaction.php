<?php

declare(strict_types = 1);

namespace Fawkes\Model;

use Fawkes\App;
use Fawkes\Database\Database;
use Fawkes\Dtos\TransactionDto;
use PDO;

class Transaction
{
    private Database $db;

    public function __construct(){
        $this->db = App::getDatabase();
    }

    /**
     * @return TransactionDto[] 
     */
    public function fetchAll() : iterable{
        $result = $this->db->query('SELECT date, "check", description, amount FROM transactions');

        $transactions = [];
        foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $transaction) {
            $transactions[] = new TransactionDto($transaction['date'], $transaction['description'], $transaction['amount'], $transaction['check']);
        }

        return $transactions;
    }

    public function saveMany(TransactionDto ...$transactions) : void{
        $rawQuery = 'INSERT INTO transactions(date, "check", description, amount) VALUES (:date, :check, :description, :amount)';

        try {
            $this->db->beginTransaction();

            foreach ($transactions as $transaction) {
                $date        = $transaction->date;
                $check       = $transaction->check;
                $description = $transaction->description;
                $amount      = (float) $transaction->amount;
    
                $statement = $this->db->prepare($rawQuery);
    
                $statement->bindParam(':date',         $date);
                $statement->bindParam(':check',        $check, $check ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $statement->bindParam(':description',  $description);
                $statement->bindParam(':amount',       $amount);

                $statement->execute();
            }

            $this->db->commit();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            if($this->db->inTransaction()){
                $this->db->rollBack();
            }
        }
        
        return;
    }

    public static function sanitizeData(TransactionDto $transaction) : TransactionDto{
        $transaction->amount = str_replace(['$', ' '], '', $transaction->amount);

        return $transaction;
    }
}