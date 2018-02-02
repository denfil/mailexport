<?php

declare(strict_types=1);

namespace MailExport;

class Pipeline
{
    /**
     * @var array
     */
    private $pipes = [];

    public function __construct(array $pipes = [])
    {
        $this->pipes = $pipes;
    }

    public function pipe(callable $pipe)
    {
        $this->pipes[] = $pipe;
        return $this;
    }

    public function process(Map $messages): Map
    {
        foreach ($this->pipes as $pipe) {
            $messages = call_user_func($pipe, $messages);
        }
        return $messages;
    }
}
