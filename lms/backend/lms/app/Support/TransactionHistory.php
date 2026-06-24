<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionHistory
{
    public static function forStudent(string $numericId): Collection
    {
        $numericId = trim($numericId);
        $sid = 'DSA'.$numericId;

        return DB::table('transaction_history')
            ->whereIn('user_id', [$numericId, $sid])
            ->orderByDesc('id')
            ->get();
    }
}
