<?php

namespace Emma\Http\Request\Containers;

use Emma\Common\Property\Property;

class SessionContainer extends Property
{
    /**
     * @param $id
     * @param $object
     */
    public function register($id, $object)
    {
        $_SESSION[$id] = $object;
        $this->register($id, $object);
    }


}