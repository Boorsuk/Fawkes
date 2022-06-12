<?php

declare(strict_types = 1);

namespace Fawkes\Controllers;

use Fawkes\Dtos\TransactionDto;
use Fawkes\Model\Transaction;
use Fawkes\View;

class HomeController
{
    public function index(){
        $view = new View('home/index');

        return $view->render();
    }
    
    public function viewTransactions(){
        $transactions = (new Transaction())->fetchAll();
        return (new View('home/transactions', ['transactions' => $transactions]))->render();
    }

    public function uploadTransactions(){
        $files = $_FILES['transaction_files'];
        $transactions = [];
        for ($i=0, $size=sizeof($files['tmp_name'] ?? []); $i < $size; $i++) {
            $error = $files['error'][$i];
            if(!$error){
                $tmpName = $files['tmp_name'][$i];
                $transactions = array_merge(TransactionDto::createFromFile($tmpName, [Transaction::class, 'sanitizeData']), $transactions);
            }
        }

        (new Transaction())->saveMany(...$transactions);

        header('Location: /transactions');
        return null;
    }
}