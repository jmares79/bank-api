<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\TransactionService;

class StoreSumAllTransactions extends Command
{
    const ERROR = "The sum of all transactions WAS NOT successful. Check server log";
    const SUCCESS = "Sum of transactions saved successfully";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:sum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stores the sum of all transactions from previous day';

    /**
     * The transaction service.
     *
     * @var TransactionService
     */
    protected $transaction;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransactionService $transaction)
    {
        parent::__construct();

        $this->transaction = $transaction;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->transaction->storeSum();
            $this->info(self::SUCCESS);
        } catch (TransactionSumStoreException $e) {
            $this->error(self::ERROR);
        }
    }
}
