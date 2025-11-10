<?php

class Headline
{
    use Model;

    protected string $table = "headlinelist";

    // Allowed columns for insert/update
    protected array $allowedColumns = [
        'text',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];

    // Allowed columns for ordering
    protected array $allowedOrderColumns = [
        'id',
        'text',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];
}
