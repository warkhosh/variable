<?php

namespace Warkhosh\Variable;

class Query extends Http
{
    /**
     * @return array
     */
    public static function query(): array
    {
        return server()->request_queries;
    }
}
