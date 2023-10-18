<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomerSaleDate implements FromView
{
    protected $data;
    public function __construct(Collection $collection){
        $this->data = $collection;
    }

    public function view(): View
    {
        return view('report.customer-sale-date', [
            'data' => $this->data
        ]);
    }
}

