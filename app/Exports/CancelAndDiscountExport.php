<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CancelAndDiscountExport implements FromView
{
    protected $data;
    public function __construct(\stdClass  $object){
        $this->data = $object;
    }

    public function view(): View
    {
        return view('report.cancel-and-discount', [
            'data' => $this->data
        ]);
    }
}

