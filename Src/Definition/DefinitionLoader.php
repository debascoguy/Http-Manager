<?php

namespace Emma\Http\Definition;

class DefinitionLoader
{

    protected array $controllersRegistry = [];

    /**
     * @return array
     */
    public function get(): array
    {
        if (empty($this->controllersRegistry)) {
            $this->controllersRegistry = (array) include __DIR__ . DIRECTORY_SEPARATOR . "Config.php";
        }
        return $this->controllersRegistry;
    }

}