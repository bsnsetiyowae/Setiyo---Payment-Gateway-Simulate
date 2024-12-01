<?php

namespace App\Services\External;

class DummyPaymentGatewayService
{
    public function deposit($datas)
    {
        if ($datas['payment_type'] == 'bank_transfer') {
            $status = 'Berhasil';
        } else {
            $status = 'Gagal';
        }
        
        return [
            'transaction_status' => $status
        ];
    }
}