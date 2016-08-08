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
        // run the tasks again for stuck tasks
        $tasks = craft()->tasks->getAllTasks();
        foreach ($tasks as $task)
        {
          craft()->tasks->rerunTaskById($task->id);
        }

        $settings = craft()->plugins->getPlugin('instacraft')->getSettings();

        if (!empty($settings['cronjobFolderId']) && !empty($settings['cronjobUrl'])) {
          craft()->instaCraft_file->save($settings['cronjobFolderId'], $settings['cronjobUrl']);
          Craft::log(Craft::t('Running cronjob.'));
          return true;
        } else {
          Craft::log(Craft::t('Please fill in the plugin settings.'));
          return false;
        }
    }
}
