<?php
namespace Craft;

class InstaCraft_FileService extends BaseApplicationComponent
{

    public $randomuseragent = array(
        "Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0",
        "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)",
        "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)",
        "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)",
        "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/4.0; InfoPath.2; SV1; .NET CLR 2.0.50727; WOW64)",
        "Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)",
        "Mozilla/4.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)"
    );

    public static $timeout = 30;

    public static $savedHeaders;

    public static $headers;

    private $tempFile;

    private $mimeTypes = array(
            'image/gif'    => '.gif',
            'image/jpeg'    => '.jpg',
            'image/png'    => '.png'
    );

    public function save($folderId, $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            $list = $this->instagramImagesToArray($url);

            if ($list) {
                foreach ($list as $url) {
                    $this->grabUrl($folderId, $url);
                }
            }
        }
    }

    public function grabUrl($folderId, $url)
    {
        $size = getimagesize($url);
        // if image is valid in php
        if (!empty($size) && !empty($size["mime"])) {
            if (craft()->userSession->isLoggedIn()) {
                if ($this->mimeTypes[$size["mime"]]) {
                    $path = pathinfo($url);

                    $addExtension = "";
                    if (empty($path["extension"])) {
                        $addExtension = $this->mimeTypes[$size["mime"]];
                    }

                    // give the image the correct name
                    $tempPath = craft()->path->getTempPath();
                    $this->tempFile = $tempPath.basename($url).$addExtension;
                    $this->tempFile = explode("?", $this->tempFile)[0];

                    // download image
                          $newImageData = $this->download($url);
                    IOHelper::writeToFile($this->tempFile, $newImageData);

                    // export tmp file to source
                    $response = craft()->assets->insertFileByLocalPath($this->tempFile, $this->tempFile, $folderId);

                    // cleanup tmp image
                          $this->deleteTempFiles($this->tempFile);

                    return true;
                }
            }
        }
        return false;
    }

    public function download($url, $saveHeaders=false, $proxy=null)
    {
        if (!empty($url)) {
            $contextvariables = array(
                'http' => array(
                    'timeout' => self::$timeout,
                    'user_agent' => $this->randomuseragent[array_rand($this->randomuseragent)],
                    'header' => self::$headers
                )
            );
            $proxyparts = explode(":", $proxy);
            if (count($proxyparts) > 1) {
                $contextvariables['http']['proxy'] = 'tcp://'.$proxy;
                $contextvariables['http']['request_fulluri'] = true;
            }
            $context = stream_context_create($contextvariables);
            $result = @file_get_contents($url, false, $context);
            if ($saveHeaders == true && $result) {
                self::$savedHeaders = $http_response_header;
            }
            return $result;
        } else {
            return false;
        }
    }

    public function instagramImagesToArray($url=null)
    {
        $data = $this->download($url);

        // instagram html/js to json
        if ($data) {
            $data = explode('window._sharedData = ', $data)[1];
            $data = explode(';</script>', $data)[0];

            $json = json_decode($data);

            foreach ($json->entry_data->ProfilePage as $user) {
                $userMedia = $user->user->media;
                if (!empty($userMedia)) {
                    $profileImages = array();
                    foreach ($userMedia->nodes as $image) {
                        $profileImages[] = $image->display_src;
                    }
                }
            }

            if (!empty($profileImages)) {
                return $profileImages;
            }
        }

        return false;
    }

    /**
     * Deletes temp files
     * @param  [string] $fileName [A filename to delete]
     */
    private function deleteTempFiles($fileName)
    {
        IOHelper::deleteFile($fileName, true);
    }
}
