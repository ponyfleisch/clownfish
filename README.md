### usage

```php
$secret = 'sharedsecret';
$s3 = 'imghosting-foo';
$cloudfrontRoot = 'https://d2rrvpvpaaof6s.cloudfront.net';

// pass bucket name and region. credentials optional.
$service = new Clownfish\Service($s3, 'ap-southeast-1', ['key' => 'foo', 'secret' => 'bar']);

// upload image
$image = $service->uploadImage('foo.jpg', 'article', 1);

// originalFilename is your reference with which you can recreate the Image Object
$originalFilename = $image->getFilename();

// output scaled version
$image = new Clownfish\Image($originalFilename);

$scaledImage = $image->getScaledImage(100, 100, $secret);

$scaledUrl = $cloudfrontRoot . $scaledImage->getPath();

echo $scaledUrl."\n";
```