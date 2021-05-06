<?php


namespace App\Objects\Reports;


class AbstractReport
{
    public    $data;
    public    $totalRecords;
    public    $totalPages;
    public    $currentPage;
    protected $dataFieldName = 'data';

    public function __construct($data, $totalRecords, $currentPage, $recordsPerPage)
    {
        $this->data         = $data;
        $this->totalRecords = $totalRecords;
        $this->totalPages   = ceil($totalRecords / $recordsPerPage);
        $this->currentPage  = $currentPage;
    }

    public function toArray()
    {
        return [
            $this->dataFieldName => $this->data,
            'total_records'      => $this->totalRecords,
            'total_pages'        => $this->totalPages,
            'current_page'       => $this->currentPage
        ];
    }


}
