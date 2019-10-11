<?php


namespace App;


class Paginator
{
    /**
     * Number of records per page
     */
    private $limit;

    /**
     * Number of records to skip
     */
    private $offset;

    /**
     * Previous page number
     */
    private $previous_page;

    /**
     * Next page number
     */
    private $next_page;

    /**
     * Number of all pages
     */
    private $total_pages;


    public function __construct(int $page, int $records_per_page, int $total_records)
    {
        $this->limit = $records_per_page;

        $page = filter_var($page, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 1,
                'min_range' => 1
            ]
        ]);

        if ($page > 1) {
            $this->previous_page = $page - 1;
        }

        $this->total_pages = (int)ceil($total_records / $records_per_page);

        if ($page < $this->total_pages) {
            $this->next_page = $page + 1;
        }

        $this->offset = $records_per_page * ($page - 1);
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getPreviousPage(): int
    {
        return $this->previous_page;
    }

    public function getNextPage(): int
    {
        return $this->next_page;
    }

    public function getTotalPages(): int
    {
        return $this->total_pages;
    }

}