<?php


namespace App\Objects\Reports;

/**
 * Class AbstractReport
 * @package App\Objects\Reports
 */
class AbstractReport
{
    public    $data;
    public    $totalRecords;
    public    $totalPages;
    public    $currentPage;
    protected $dataFieldName = 'data';

    /**
     * AbstractReport constructor.
     * @param $data
     * @param $totalRecords
     * @param $currentPage
     * @param $recordsPerPage
     */
    public function __construct($data, $totalRecords, $currentPage, $recordsPerPage)
    {
        $this->data         = $data;
        $this->totalRecords = $totalRecords;
        $this->totalPages   = ceil($totalRecords / $recordsPerPage);
        $this->currentPage  = $currentPage;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->dataFieldName => $this->data,
            'total_records'      => $this->totalRecords,
            'total_pages'        => $this->totalPages,
            'current_page'       => $this->currentPage
        ];
    }


}
