<?php
namespace Craft;

class ImportTask extends BaseTask
{

    protected function defineSettings()
    {
        return array(
            'folderId' => AttributeType::Number,
            'total' => AttributeType::Number,
            'url' => AttributeType::Name,
        );
    }

    public function getDescription()
    {
        return Craft::t('InstaCraft');
    }

    public function getTotalSteps()
    {
        $settings = $this->getSettings();
        return $settings->total;
    }

    public function runStep($step)
    {
        $settings = $this->getSettings();
        // Grab the url
        craft()->instaCraft_file->grabUrl($settings->folderId, $settings->url);

        return true;
    }
}
