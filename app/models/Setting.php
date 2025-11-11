<?php

class Setting
{
    use Model;

    protected string $table = "settings";
    protected array $allowedColumns = [
        'id',
        'key',
        'value',
        'status',
        'updated_at',
        'created_at',
    ];
    
    protected array $allowedOrderColumns = [
        'id',
        'key',
        'value',
        'status',
        'updated_at',
        'created_at',
    ];
}
