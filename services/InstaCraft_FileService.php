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

    private $mimeTypes = array(
        'image/gif'    => '.gif',
        'image/jpeg'    => '.jpg',
        'image/png'    => '.png'
    );

    /**
     * Create a task to save an image to a specified folder / source
     * @param  int $folderId    the id of the folder where the images must be stored
     * @param  string $url      the url of the instagram image
     * @return boolean          returns true if every url in the list is looped
     */
    public function save($folderId, $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            $list = $this->scrape($folderId, $url);
            if ($list) {
                foreach ($list as $object) {
                    craft()->tasks->createTask('InstaCraft_File', Craft::t('Downloading: ').$object['display_src'], array(
                        'folderId' => (int)$folderId,
                        'imageId' => $object['id'],
                        'url' => $object['display_src'],
                        'text' => $object['caption'],
                    ));
                }
                if (craft()->userSession->getUser()) {
                    craft()->userSession->setNotice(Craft::t('Downloading started.'));
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Download the image
     * @param  string $url      a url to download
     * @param  string $imageId  the instagram imageid for the filename
     * @return mixed            return if it saved the image
     */
    public function downloadImage($url, $imageId='') {
        $size = getimagesize($url);
        // if image is valid in php
        if (!empty($size) && !empty($size["mime"])) {
          $newImageData = $this->download($url);
          if (!empty($newImageData)) {
              return IOHelper::writeToFile(CRAFT_STORAGE_PATH.(string)$imageId.'.jpg', $newImageData);
          }
        }
        return false;
    }

    /**
     * Move the image to your image destination (this can be for example S3)
     * @param  integer $folderId  to save it to the folderid folder
     * @param  string  $imageId   the instagram image id
     * @return boolean            return if it got a response in a boolean
     */
    public function moveImage($folderId=0, $imageId='') {
        $response = craft()->assets->insertFileByLocalPath(CRAFT_STORAGE_PATH.(string)$imageId.'.jpg', (string)$imageId.'.jpg', (int)$folderId);
        if (!empty($response)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove the temporary image
     * @param  string $imageId the instagram id of and image to remove
     * @return boolean return if the deletion of the file has success
     */
    public function removeTmpImage($imageId='') {
        return $this->deleteTempFiles((string)$imageId.'.jpg');
    }

    /**
     * Download an url with a random useragent
     * @param  string $url          the url to download
     * @param  boolean $saveHeaders if you want the headers to be stored in a static variable set this to true
     * @param  string $proxy        you can put a proxy here to make requests with a proxy
     * @return mixed                this returns false if the url is empty. And this will return the request result if everything is going well
     */
    private function download($url, $saveHeaders=false, $proxy=null)
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

    /**
     * Scrape the json from an instagram page and return this to an array
     * @param  integer $folderId the folder id to check if the file exists
     * @param  string  $url      you need to put a valid instagram url in this variable
     * @return mixed             this will return an array with instagram profile images
     */
    public function scrape($folderId=0, $url=null)
    {
        $data = $this->download($url);

        // instagram html/js to loopable array
        if ($data) {
            $data = explode('window._sharedData = ', $data)[1];
            $data = explode(';</script>', $data)[0];

            $json = json_decode($data);

            foreach ($json->entry_data->ProfilePage as $user) {
                $userMedia = $user->user->media;
                if (!empty($userMedia)) {
                    $profileImages = array();
                    foreach ($userMedia->nodes as $key => $image) {
                        $instagramUrl = explode('?', $image->display_src)[0];
                        // if the file not exists in source
                        $fileFound = $this->fileExists($folderId, $image->id);
                        if (!$fileFound) {
                            $profileImages[$key]['display_src'] = $instagramUrl;
                            $profileImages[$key]['caption'] = $image->caption;
                            $profileImages[$key]['id'] = $image->id;
                        }
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
     * If the file exists in the source
     * @param  integer $folderId the id of the source it is located in
     * @param  string  $imageId  the instagram id of the image
     * @return boolean           if the filename exists or not
     */
    public function fileExists($folderId=0, $imageId='') {
        // TODO fix the extension .jpg
        $file = craft()->assets->findFile(array('folderId' => $folderId, 'filename' => $imageId.'.jpg'));
        if (!empty($file->filename) && $file->filename == $imageId.'.jpg') {
          return true;
        } else {
          return false;
        }
    }

    /**
     * Delete a file
     */
    private function deleteTempFiles($fileName)
    {
        return IOHelper::deleteFile($fileName, true);
    }
}
