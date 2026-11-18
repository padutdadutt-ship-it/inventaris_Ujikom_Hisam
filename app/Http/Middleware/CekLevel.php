<?php

namespace App\Http\Middleware;

use App\Models\Petugas;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekLevel
{
    /**
     * Pemakaian di route: ->middleware('level:admin,operator')
     */
    public function handle(Request $request, Closure $next, string ...$levels)
    {
        $peta = [
            'admin'    => Petugas::ADMIN,
            'operator' => Petugas::OPERATOR,
            'peminjam' => Petugas::PEMINJAM,
        ];

        $diizinkan = array_map(fn ($nama) => $peta[$nama] ?? null, $levels);

        if (! in_array((int) Auth::user()->id_level, $diizinkan, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}   