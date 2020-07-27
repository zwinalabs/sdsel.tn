<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 */

defined('_AKEEBA') or die();

class AngieControllerBaseMain extends AController
{
    /**
     * Try to detect the CMS version
     */
    public function detectversion()
    {
        /** @var AngieModelBaseMain $model */
        $model = $this->getThisModel();
        $model->detectVersion();

	    @ob_clean();
        echo json_encode(true);
    }

    public function startover()
    {
        $this->container->session->reset();
        $this->container->session->saveData();
        $this->setRedirect('index.php?view=main');
    }
}
