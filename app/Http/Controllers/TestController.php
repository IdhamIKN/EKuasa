<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function testQueries()
    {
        // 1. Tampilkan semua member
        $members = DB::connection('mysql2')->table('tblmember')->get();

        $memberId = '0328454933';
        $totalPoint = DB::connection('mysql2')
            ->table('tbltransmember as a')
            ->join('tblmember as b', 'a.member', '=', 'b.member')
            ->where('a.member', $memberId)
            ->select('a.member', DB::raw('SUM(a.poinm - a.poink) AS tpoint'))
            ->groupBy('a.member')
            ->first();


        $laporan = DB::connection('mysql2')
            ->table('tbltransmember as a')
            ->where('a.member', $memberId)
            ->select(
                'noreff',
                'waktu',
                'ket',
                DB::raw('(poinm - poink) AS poin'),
                DB::raw("IF(poink = 0, 'poin masuk', 'poin keluar') AS ketpoin")
            )
            ->orderByDesc('notrx')
            ->get();


        return response()->json([
            // 'members' => $members,
            'total_point' => $totalPoint,
            'laporan' => $laporan
        ]);
    }
}
