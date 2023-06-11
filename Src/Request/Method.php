<?php

namespace Emma\Http\Request;

class Method
{
    public const POST = "POST";

    public const GET = "GET";

    public const OPTIONS = "OPTIONS";

    public const PATCH = "PATCH";

    public const DELETE = "DELETE";

    public const PUT = "PUT";

    public const HEAD = "HEAD";

    /**
     * @return string[]|array
     */
    public static function all(): array
    {
        return [
            self::POST,
            self::GET,
            self::OPTIONS,
            self::PATCH,
            self::DELETE,
            self::PUT,
            self::HEAD,
        ];
    }

}