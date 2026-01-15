<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\User;

class TransactionController extends Controller
{
    public function manage(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
            'type' => 'required|in:deposit,withdraw',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $amount = $request->amount;
        $type = $request->type;

        if ($type == 'withdraw' && $user->balance < $amount) {
            return response()->json(['message' => 'ยอดเงินในบัญชีไม่เพียงพอ'], 400);
        }

        DB::beginTransaction();

        try {
            Transaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
            ]);

            if ($type == 'deposit') {
                $user->balance += $amount;
            } else {
                $user->balance -= $amount;
            }
            $user->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'ทำรายการสำเร็จ',
                'balance' => $user->balance,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
}