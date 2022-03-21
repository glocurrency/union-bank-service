<?php

namespace GloCurrency\UnionBank\Console;

use Illuminate\Console\Command;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Jobs\FetchTransactionUpdateJob;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;

class FetchTransactionsUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'globus:fetch-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch fetch jobs for unfinished UnionBank/Transaction';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @todo add actual implemetation
     */
    public function handle()
    {
        $transactionsQuery = Transaction::where('state_code', TransactionStateCodeEnum::PROCESSING->value);

        $count = $transactionsQuery->count();

        if (!$count) {
            $this->error('You do not have any unfinished UnionBank/Transaction');
            return;
        }

        $this->info("Dispatching fetch jobs for {$count} UnionBank/Transaction");

        $bar = $this->output->createProgressBar($count);

        $bar->start();

        foreach ($transactionsQuery->cursor() as $transaction) {
            FetchTransactionUpdateJob::dispatch($transaction);
        }

        $bar->finish();

        $this->newLine();
        $this->info("Job dispatching done");
    }
}
