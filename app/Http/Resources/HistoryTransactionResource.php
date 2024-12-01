<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user->name,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => ($this->status === 1) ? 'success' : 'failed',
            'datetime' => $this->updated_at,
        ];

    }
}
