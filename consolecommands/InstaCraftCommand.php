<?php
namespace Craft;

class InstaCraftCommand extends BaseCommand
{
    /**
     * Runs cronjob for instacraft
     *
     * @return int
     */
    public function actionRun()
    {
        $settings = craft()->plugins->getPlugin('instacraft')->getSettings();

        craft()->instaCraft_file->save($settings['cronjobFolderId'], $settings['cronjobUrl']);

        Craft::log(Craft::t('Running cronjob.'));

        return true;
    }
}
