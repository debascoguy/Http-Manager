<?php

namespace Emma\Http\Request\Escaper;

class Escaper
{
    protected const UTF8_ENCODING = "UTF-8";

    protected const DOUBLE_ENCODE = true;

    /**
     * @param string $string
     * @param string $encoding
     * @param bool $doubleEncode
     * @return string
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     * @see https://www.php.net/manual/en/function.mb-convert-encoding.php
     */
    public static function escapeData(string $string,
                                      string $encoding = self::UTF8_ENCODING,
                                      bool   $doubleEncode = self::DOUBLE_ENCODE
    ): string {
        return trim(
            htmlspecialchars(
                mb_convert_encoding(
                    $string, 'UTF-8', ['UTF-8', 'Windows-1252']
                ),
                ENT_QUOTES | ENT_SUBSTITUTE,
                $encoding ?? self::UTF8_ENCODING,
                $doubleEncode
            )
        );
    }

    /**
     * @param array $params
     * @param string $charset
     * @param bool $doubleEncode
     * @return array
     */
    public static function escapeAll(array $params = [], string $charset = "UTF-8", bool $doubleEncode = true): array
    {
        $escapedParams = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $escapedParams[$key] = self::escapeAll($value, $charset, $doubleEncode);
            } else {
                $escapedParams[$key] = self::escapeData($value, $charset, $doubleEncode);
            }
        }

        return $escapedParams;
    }

}