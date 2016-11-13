<?php

namespace Clownfish;

class Image
{
    protected $filename;
    protected $width;
    protected $height;
    protected $hash;
    protected $space;
    protected $id;
    protected $extension;

    public function __construct($filename)
    {
        $this->parseFilename($filename);
    }

    protected function parseFilename($filename)
    {
        $matches = [];
        preg_match('/^(\w+)-(\w+)-(\w+)-(\d+)-(\d+)\.(\w+)$/', $filename, $matches);
        
        if(count($matches) != 7){
            throw new InvalidFilenameException('Cannot parse filename '.$filename);
        }

        list(
            $this->filename,
            $this->hash,
            $this->space,
            $this->id,
            $this->width,
            $this->height,
            $this->extension) = $matches;
    }

    public function getOriginalPath()
    {
        return '/o/'.$this->getFilename();
    }

    /**
     * @param int $width
     * @param int $height
     * @param string|null $secret
     * @param string|null $mode
     * @return ScaledImage
     * @throws InvalidImageConfigurationException
     */
    public function getScaledImage($width, $height, $secret=null, $mode='m')
    {
        if(!is_int($width) || !is_int($height) || $width <= 0 || $height <= 0){
            throw new InvalidImageConfigurationException('Invalid image dimensions: '.$width.' x '.$height);
        }

        if($mode != 'm' && $mode != 'c'){
            throw new InvalidImageConfigurationException('Invalid scaling mode: '.$mode);
        }

        if($mode == 'm'){
            $widthRatio = $width/$this->width;
            $heightRatio = $width/$this->height;

            if($widthRatio < $heightRatio){
                $newWidth = $width;
                $newHeight = round($this->height * $widthRatio);
            }else{
                $newHeight = $height;
                $newWidth = round($this->width * $heightRatio);
            }
        }else{
            $newWidth = $width;
            $newHeight = $height;
        }

        $path = '/s/'.$this->getHash().'-'.$this->getSpace().'-'.$this->getId().'-'.$this->getWidth().'-'.$this->getHeight().'/'.$newWidth.'x'.$newHeight.$mode.'.'.$this->getExtension();

        if($secret){
            $hash = base64_encode(hash_hmac('sha1', $path, $secret, true));
            $path .= '?'.urlencode($hash);
        }

        return new ScaledImage($path, $newWidth, $newHeight);
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }
}