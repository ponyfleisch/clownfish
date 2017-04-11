<?php

namespace Clownfish;
use Aws\S3\S3Client;

class Service {

    protected $client;
    protected $bucket;

    public function __construct($bucket, $region, $credentials)
    {
        $config = ['region' => $region, 'version' => '2006-03-01'];
        if($credentials){
            $config['credentials'] = $credentials;
        }
        $this->client = new S3Client($config);
        $this->bucket = $bucket;
    }


    /**
     * @param $filename
     * @return Image
     */
    public function uploadImage($filename, $space, $id)
    {
        if(!file_exists($filename)){
            throw new FileNotFoundException('file not found: '.$filename);
        }

        if(!strlen($space) || !strlen($id)){
            throw new InvalidParametersException('parameters not valid: id '.$id.' space '.$space);
        }
        
        list($width, $height, $type) = getimagesize($filename);

        if(!$width){
            throw new InvalidFilenameException('file is not an image: '.$filename);
        }

        switch($type){
            case IMAGETYPE_JPEG2000:
            case IMAGETYPE_JPEG:
                $ext = 'jpg';
                $contentType = 'image/jpeg';
                break;
            case IMAGETYPE_PNG:
                $ext = 'png';
                $contentType = 'image/png';
                break;
            default:
                throw new UnsupportedImagetypeException('image type not supported: '.$type);
        }

        $contentHash = sha1_file($filename);

        $targetName = implode('-', [$contentHash, $space, $id, $width, $height]).'.'.$ext;
        $key = 'o/'.$targetName;

        // exponential backoff for now.
        $retries = 5;
        while($retries > 0) {
            $result = $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $filename,
                'ContentType' => $contentType,
                'CacheControl' => 'max-age=31536000', // 1 year
            ]);
            if ($result->get('ObjectURL')) {
                return new Image($targetName);
            } else {
                sleep(pow(6 - $retries, 2));
                $retries--;
            }
        }
    }

    public function deleteImage($filename)
    {
        if($filename != ''){
            $key = 'o/'.$filename;
            $result = $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key
            ]);

            return true;
        }else{
            return false;
        }
    }
}