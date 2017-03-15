<?php
define('IMAGES_DIR', 'img');
if (!isset($_POST['func'])) $_POST['func'] = '';
if (isset($_FILES['uploadfile']['tmp_name'])) {
    $dir = __DIR__ . '/' . IMAGES_DIR . '/';
    $urls = file($_FILES['uploadfile']['tmp_name']);
    $loadedFiles = array();
    foreach ($urls as $url) {
        $url = trim($url);
        $url = filter_var($url, FILTER_VALIDATE_URL);
        if ($url) {
            $ext = explode('.', $url);
            $ext = end($ext);
            $loadedFiles[] = md5($url) . '.' . $ext;
            $fileName = $dir . md5($url) . '.' . $ext;
            if (!file_exists($fileName)) {
                if (!is_dir($dir)) mkdir($dir, 0777);
                $img = @file_get_contents($url);
                if ($img) {
                    file_put_contents($fileName, $img);
                    $imageData = read_exif_data($fileName);
                    if ($imageData) {
                        if (in_array($imageData['mime'], array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))) file_put_contents($fileName, $img);
                    } else {
                        unlink($fileName);
                    }
                }
            }
        }
    }
    returnImages($loadedFiles);
}
switch ($_POST['func']) {
    case('init'):
        returnImages();
        break;
}
function returnImages($loadedFiles = array())
{
    $dir = __DIR__ . '/' . IMAGES_DIR . '/';
    $images = array();
    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if (preg_match('|^[a-z0-9]{32}\.[a-z]{3,4}$|i', $entry) && (in_array($entry, $loadedFiles) || empty($loadedFiles))) {
                $data = resizeImage($dir . $entry);
                $images[] = array('url' => '/img/' . $entry, 'origWidth' => $data['width'], 'origHeight' => $data['height']);
            }
        }
        closedir($handle);
    }
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($images);
    exit;
}

function resizeImage($fileName)
{
    $imageData = getimagesize($fileName);
    $newHeight = 200;
    $newWidth = round($imageData[0] * $newHeight / $imageData[1]);
    $dest = imagecreatetruecolor($newWidth, $newHeight);
    $wm = imagecreatefrompng('wm.png');
    $wmData = getimagesize('wm.png');

    switch ($imageData['mime']) {
        case('image/jpg'):
        case('image/jpeg'):
            $func1 = 'imagecreatefromjpeg';
            $func2 = 'imagejpeg';
            break;
        case('image/png'):
            $func1 = 'imagecreatefrompng';
            $func2 = 'imagepng';
            break;
        case('image/gif'):
            $func1 = 'imagecreatefromgif';
            $func2 = 'imagegif';
            break;
    }
    $src = @$func1($fileName) or exit(json_encode(array('status' => false)));
    @imagecopyresampled($dest, $src, 0, 0, 0, 0, $newWidth, $newHeight, $imageData[0], $imageData[1]);
    @imagecopyresampled($dest, $wm, 0, 0, 0, 0, $wmData[0] / 4, $wmData[1] / 4, $wmData[0], $wmData[1]);
    @$func2($dest, $fileName) or exit(json_encode(array('status' => false)));
    imagedestroy($dest);
    imagedestroy($src);
    imagedestroy($wm);
    return array('width' => $newWidth, 'height' => $newHeight);
}

?>