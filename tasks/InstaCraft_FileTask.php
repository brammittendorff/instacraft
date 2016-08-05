<?php
namespace Craft;

class InstaCraft_FileTask extends BaseTask
{

    /**
     * Defined settings
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'folderId' => AttributeType::Number,
            'imageId' => AttributeType::String,
            'url' => AttributeType::String,
            'text' => AttributeType::String,
        );
    }

    /**
     * Return description
     * @return string
     */
    public function getDescription()
    {
        return Craft::t('Instagram download');
    }

    /**
     * Total steps to run
     * @return int
     */
    public function getTotalSteps()
    {
        return 3;
    }

    /**
     * Run each step
     * @param  int  $step
     * @return boolean
     */
    public function runStep($step)
    {
        $settings = $this->getSettings();

        switch ($step) {
            case 0:
                return craft()->instaCraft_file->downloadImage($settings->url, $settings->imageId);
            break;
            case 1:
                return craft()->instaCraft_file->moveImage($settings->folderId);
            break;
            case 2:
                return craft()->instaCraft_file->removeTmpImage();
            break;
        }
    }
}
