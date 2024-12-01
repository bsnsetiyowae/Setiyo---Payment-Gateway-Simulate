<?php

namespace App\Jobs;

use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWalletBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $amount;
    protected $operation; // 'deposit' or 'withdrawal'

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $amount, $operation)
    {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->operation = $operation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Mengunci wallet untuk memastikan tidak ada yang mengaksesnya secara bersamaan
        $wallet = Wallet::where('user_id', $this->userId)->lockForUpdate()->first();
        if (empty($wallet?->id)) {
            Wallet::create([
                'user_id' => $this->userId,
                'balance' => $this->amount,
            ]);
        } else {
            if ($this->operation == 'deposit') {
                $wallet->balance += $this->amount;
            } elseif ($this->operation == 'withdrawal') {
                $wallet->balance -= $this->amount;
            }
    
            // Simpan pembaruan saldo
            $wallet->save();    
        }
    }
}
