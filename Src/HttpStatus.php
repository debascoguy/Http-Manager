<?php
namespace Emma\Http;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class HttpStatus
{
    /** HTTP Response Constants */
    const HTTP_OK = 200;
    const HTTP_FOUND = 302;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_CONTENT_TYPE_NOT_ACCEPTED = 406;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_INVALID_INPUT = 409;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;

    /**
     * @var array
     */
    public static $statusTexts = array(
        self::HTTP_OK => 'OK',
        self::HTTP_FOUND => 'Found',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_FORBIDDEN => 'Forbidden',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::HTTP_CONTENT_TYPE_NOT_ACCEPTED => 'Content Type Not Accepted',
        self::HTTP_REQUEST_TIMEOUT => 'Request Timeout',
        self::HTTP_INVALID_INPUT => 'Invalid Input',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED => 'Not Implemented',
    );

    public static function getStatusText($statusCode)
    {
        return self::$statusTexts[$statusCode];
    }

}