<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesByTypeExport implements FromView
{
    protected $data;
    public function __construct(Collection $collection){
        $this->data = $collection;
    }

    public function view(): View
    {
        return view('report.sales-by-type', [
            'data' => $this->data
        ]);
    }
}

