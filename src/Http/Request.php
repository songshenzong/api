<?php

namespace Songshenzong\ResponseJson\Http;

use Songshenzong\ResponseJson\Http\Parser\Accept;
use Illuminate\Http\Request as IlluminateRequest;

class Request extends IlluminateRequest
{
    /**
     * Accept parser instance.
     *
     */
    protected static $acceptParser;

    /**
     * Parsed accept header for the request.
     *
     * @var array
     */
    protected $accept;


    /**
     * Create a new request instance from an Illuminate request instance.
     *
     * @param \Illuminate\Http\Request $old
     *
     * @return static
     */
    public function createFromIlluminate(IlluminateRequest $old)
    {
        $new = new static(
            $old -> query -> all(), $old -> request -> all(), $old -> attributes -> all(),
            $old -> cookies -> all(), $old -> files -> all(), $old -> server -> all(), $old -> content
        );

        if ($session = $old -> getSession()) {
            $new -> setSession($old -> getSession());
        }

        $new -> setRouteResolver($old -> getRouteResolver());
        $new -> setUserResolver($old -> getUserResolver());

        return $new;
    }

    /**
     * Get the defined version.
     *
     * @return string
     */
    public function version()
    {
        $this -> parseAcceptHeader();

        return $this -> accept['version'];
    }

    /**
     * Get the defined subtype.
     *
     * @return string
     */
    public function subtype()
    {
        $this -> parseAcceptHeader();

        return $this -> accept['subtype'];
    }

    /**
     * Get the expected format type.
     *
     * @return string
     */
    public function format($default = 'html')
    {
        $this -> parseAcceptHeader();

        return $this -> accept['format'] ?: parent ::format($default);
    }

    /**
     * Parse the accept header.
     *
     * @return void
     */
    protected function parseAcceptHeader()
    {
        if ($this -> accept) {
            return;
        }

        $this -> accept = static ::$acceptParser -> parse($this);
    }

    /**
     * Set the accept parser instance.
     *
     *
     * @return void
     */
    public static function setAcceptParser(Accept $acceptParser)
    {
        static ::$acceptParser = $acceptParser;
    }

    /**
     * Get the accept parser instance.
     *
     */
    public static function getAcceptParser()
    {
        return static ::$acceptParser;
    }
}
