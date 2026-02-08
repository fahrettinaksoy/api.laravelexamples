<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Throw on Invalid Filter
    |--------------------------------------------------------------------------
    |
    | When true, SmartQuery will throw an exception when a filter is requested
    | that has not been explicitly allowed. When false, invalid filters will
    | be silently ignored.
    |
    */
    'throw_on_invalid_filter' => env('SMARTQUERY_THROW_ON_INVALID_FILTER', true),

    /*
    |--------------------------------------------------------------------------
    | Throw on Invalid Sort
    |--------------------------------------------------------------------------
    |
    | When true, SmartQuery will throw an exception when a sort is requested
    | that has not been explicitly allowed. When false, invalid sorts will
    | be silently ignored.
    |
    */
    'throw_on_invalid_sort' => env('SMARTQUERY_THROW_ON_INVALID_SORT', true),

    /*
    |--------------------------------------------------------------------------
    | Throw on Invalid Include
    |--------------------------------------------------------------------------
    |
    | When true, SmartQuery will throw an exception when an include is requested
    | that has not been explicitly allowed. When false, invalid includes will
    | be silently ignored.
    |
    */
    'throw_on_invalid_include' => env('SMARTQUERY_THROW_ON_INVALID_INCLUDE', true),

    /*
    |--------------------------------------------------------------------------
    | Throw on Invalid Field
    |--------------------------------------------------------------------------
    |
    | When true, SmartQuery will throw an exception when a field is requested
    | that has not been explicitly allowed. When false, invalid fields will
    | be silently ignored.
    |
    */
    'throw_on_invalid_field' => env('SMARTQUERY_THROW_ON_INVALID_FIELD', true),
];
