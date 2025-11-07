<?php

class HeadLine
{
    use Model;

    protected string $table = "headlinelist";

    // Allowed columns for insert/update
    protected array $allowedColumns = [
        'text',
        'description',
        'created_at',
        'updated_at'
    ];

    // Allowed columns for ordering
    protected array $allowedOrderColumns = [
        'id',
        'text',
        'description',
        'created_at',
        'updated_at'
    ];
}
