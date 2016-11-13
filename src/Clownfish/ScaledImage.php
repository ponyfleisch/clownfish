<?php

namespace Clownfish;

class ScaledImage
{
    /** @var string */
    protected $path;

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    public function __construct($path, $width, $height)
    {
        $this->path = $path;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}