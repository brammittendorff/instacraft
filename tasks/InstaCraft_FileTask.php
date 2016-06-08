<?php
namespace Craft;

class InstaCraft_FileTask extends BaseTask
{

    protected function defineSettings()
    {
        return array(
            'folderId' => AttributeType::Number,
            'total' => AttributeType::Number,
            'url' => AttributeType::String,
        );
    }

    public function getDescription()
    {
        return Craft::t('Instagram download');
    }


    public function getTotalSteps()
    {
        return 1;
    }

    public function runStep($step)
    {
        $settings = $this->getSettings();
        return craft()->instaCraft_file->generate($settings->folderId, $settings->url);
    }
}
