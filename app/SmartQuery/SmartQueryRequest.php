<?php

declare(strict_types=1);

namespace App\SmartQuery;

class SmartQueryRequest
{
    protected static string $arrayValueDelimiter = ',';

    protected static string $filterArrayValueDelimiter = ',';

    protected static string $sortsArrayValueDelimiter = ',';

    protected static string $includesArrayValueDelimiter = ',';

    protected static string $fieldsArrayValueDelimiter = ',';

    protected static string $appendsArrayValueDelimiter = ',';

    public static function setArrayValueDelimiter(string $delimiter): void
    {
        static::$arrayValueDelimiter = $delimiter;
        static::$filterArrayValueDelimiter = $delimiter;
        static::$sortsArrayValueDelimiter = $delimiter;
        static::$includesArrayValueDelimiter = $delimiter;
        static::$fieldsArrayValueDelimiter = $delimiter;
        static::$appendsArrayValueDelimiter = $delimiter;
    }

    public static function setFilterArrayValueDelimiter(string $delimiter): void
    {
        static::$filterArrayValueDelimiter = $delimiter;
    }

    public static function setSortsArrayValueDelimiter(string $delimiter): void
    {
        static::$sortsArrayValueDelimiter = $delimiter;
    }

    public static function setIncludesArrayValueDelimiter(string $delimiter): void
    {
        static::$includesArrayValueDelimiter = $delimiter;
    }

    public static function setFieldsArrayValueDelimiter(string $delimiter): void
    {
        static::$fieldsArrayValueDelimiter = $delimiter;
    }

    public static function setAppendsArrayValueDelimiter(string $delimiter): void
    {
        static::$appendsArrayValueDelimiter = $delimiter;
    }

    public static function getFilterArrayValueDelimiter(): string
    {
        return static::$filterArrayValueDelimiter;
    }

    public static function getSortsArrayValueDelimiter(): string
    {
        return static::$sortsArrayValueDelimiter;
    }

    public static function getIncludesArrayValueDelimiter(): string
    {
        return static::$includesArrayValueDelimiter;
    }

    public static function getFieldsArrayValueDelimiter(): string
    {
        return static::$fieldsArrayValueDelimiter;
    }

    public static function getAppendsArrayValueDelimiter(): string
    {
        return static::$appendsArrayValueDelimiter;
    }
}
