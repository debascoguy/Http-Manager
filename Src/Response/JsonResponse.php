<?php
namespace Emma\Http\Response;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class JsonResponse extends Response
{
    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data = array())
    {
        $this->setJson($data);
    }

}