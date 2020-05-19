<?php

namespace App\Exports;

use App\Envio;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EnviosExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.envios', [
            'envios' => Envio::all()
        ]);
    }
}
