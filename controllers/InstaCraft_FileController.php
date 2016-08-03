<?php
namespace Craft;

class InstaCraft_FileController extends BaseController
{
    protected $allowAnonymous = array('actionDownload');

    /**
     * Trigger the download
     */
    public function actionDownload()
    {
        $url = craft()->request->getPost('url');
        $source = craft()->request->getPost('assetSource');

        if (craft()->userSession->isLoggedIn()) {
            // download the instagram images
            craft()->instaCraft_file->save($source, $url);
        }
    }
}
