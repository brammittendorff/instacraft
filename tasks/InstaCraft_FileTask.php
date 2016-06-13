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
            'url' => AttributeType::String,
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
        return 1;
    }

    /**
     * Run each step
     * @param  int  $step
     * @return boolean
     */
    public function runStep($step)
    {
        $settings = $this->getSettings();
        return craft()->instaCraft_file->generate($settings->folderId, $settings->url);
    }
}
