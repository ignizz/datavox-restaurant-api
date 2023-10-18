<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class CollectionExport implements FromCollection
{
    protected $data;
    public function __construct(Collection $collection){
        $this->data = $collection;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }
}
