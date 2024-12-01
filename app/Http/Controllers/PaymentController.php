<?php

namespace App\Http\Controllers;

use App\Http\Resources\HistoryTransactionResource;
use App\Jobs\UpdateWalletBalance;
use App\Models\Transaction;
use App\Services\External\DummyPaymentGatewayService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\CoreApi;

class PaymentController extends Controller
{

    /**
     * Create the component instance.
     */
    public function __construct(
        public DummyPaymentGatewayService $dummyPaymentGatewayService = new DummyPaymentGatewayService(),
    ) {
    }

    public function deposit(Request $request)
    {
        $user = auth()->user();

        // Validasi input
        $request->validate([
            'order_id' => 'required|string|unique:transactions,order_id',
            'amount' => 'required|numeric|min:1',
        ]);


        // Data untuk transaksi
        $order_id = $request->order_id;
        $amount = $request->amount;
        $timestamp = now();

        // Buat payload untuk request ke Dummy Payment Gateway
        $payload = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        try {
            // Kirim request ke Dummy Payment Gateway
            $response = $this->dummyPaymentGatewayService->deposit($payload);

            // Simpan transaksi ke database
            $status = ($response['transaction_status'] === 'Berhasil') ? 1 : 2;
            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $order_id,
                'amount' => $amount,
                'timestamp' => $timestamp,
                'status' => $status,
                'type' => 'deposit',
            ]);

            // Update saldo Update Saldo wallet secara Asynchronous
            // Dengan job mencegah transaksi overlapping
            if ($status === 1) {
                dispatch(new UpdateWalletBalance($user->id, $amount, 'deposit'));
            }

            return response()->json(['message' => 'Deposit berhasil', 'data' => $response], 200);

        } catch (\Exception $e) {
            // Tangani error jika terjadi
            return response()->json(['message' => 'Deposit gagal', 'error' => $e->getMessage()], 500);
        }
    }
  
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        try {
            $user = auth()->user();
            $wallet = $user->wallet;
            $amount = $request->amount;
    
            if ($wallet?->balance < $amount) {
                throw new Exception("Saldo tidak mencukupi", 1);
            }
            dispatch(function () use ($wallet, $amount) {
                DB::transaction(function () use ($wallet, $amount) {
                    $wallet->balance -= $amount;
                    $wallet->save();
                });
            });
    
            $Transaction = Transaction::create([
                'user_id' => $user->id,
                'order_id' => uniqid(),
                'amount' => $amount,
                'timestamp' => now(),
                'status' => 1,
                'type' => 'withdraw',
            ]);
    
            return response()->json(['message' => 'Penarikan berhasil']);    
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);    
        }
    }

    public function transactionHistory(Request $request) {
        // Dapatkan transaksi user yang sedang login
        $user = auth()->user();

        // Ambil data transaksi user dengan pagination
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->with('user:id,name')
            ->paginate(10); // Tampilkan 10 transaksi per halaman

        // return response()->json($transactions, 200);
        return response()->json(HistoryTransactionResource::collection($transactions), 200);

    }
}
