<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class EWalletController extends Controller
{
    public function getBalance()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'wallet_balance' => number_format($user->wallet_balance, 2)
            ]
        ], 200);
    }
}
