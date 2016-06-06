<?php
namespace Craft;

class InstaCraft_FileController extends BaseController
{
    protected $allowAnonymous = array('actionDownload');

    public function actionDownload()
    {
        $url = craft()->request->getPost('url');
        $source = craft()->request->getPost('assetSource');

        if (craft()->instaCraft_file->save($source, $url)) {
        } else {
        }
    }
}
