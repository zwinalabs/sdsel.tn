<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 */

defined('_AKEEBA') or die();

/** @var $this AngieViewDatabase */

$document = $this->container->application->getDocument();

$this->loadHelper('select');

$document->addScript('angie/js/json.js');
$document->addScript('angie/js/ajax.js');
$document->addScript('angie/js/database.js');

$url             = 'index.php';
$dbPassMessage   = AText::_('DATABASE_ERR_COMPLEXPASSWORD');
$dbPassMessage   = str_replace(array("\n", "'"), array('\\n', '\\\''), $dbPassMessage);
$dbPrefixMessage = AText::_('DATABASE_ERR_UPPERCASEPREFIX');
$dbPrefixMessage = str_replace(array("\n", "'"), array('\\n', '\\\''), $dbPrefixMessage);
$dbuserEscaped   = addcslashes($this->db->dbuser, '\'\\');
$dbpassEscaped   = addcslashes($this->db->dbpass, '\'\\');

$header = '';

if ($this->number_of_substeps)
{
    $header = AText::_('DATABASE_HEADER_MASTER_MAINDB');

    if ($this->substep != 'site.sql')
    {
        $header = AText::sprintf('DATABASE_HEADER_MASTER', $this->substep);
    }
}

$document->addScriptDeclaration(<<<JS
var akeebaAjax = null;

function angieRestoreDefaultDatabaseOptions()
{
	var elDBUser = document.getElementById('dbuser');
	var elDBPass = document.getElementById('dbpass');
	
	// Chrome auto-fills fields it THINKS are a login form. We need to restore these values. However, we can't just do
	// that, because if the real value is empty Chrome will simply ignore us. So we have to set them to a dummy value
	// and then to the real value. Writing web software is easy. Working around all the ways the web is broken is not.
	elDBUser.value = 'IGNORE ME';
	elDBPass.value = 'IGNORE ME';
	// And now the real value, at last
	elDBUser.value = '$dbuserEscaped';
	elDBPass.value = '$dbpassEscaped';
}

akeeba.System.documentReady(function ()
{
	akeebaAjax = new akeebaAjaxConnector('$url');

	databasePasswordMessage = '$dbPassMessage';
	databasePrefixMessage = '$dbPrefixMessage';
	
	setTimeout(angieRestoreDefaultDatabaseOptions, 500);
});

JS
);

echo $this->loadAnyTemplate('steps/buttons');
echo $this->loadAnyTemplate('steps/steps', array('helpurl' => 'https://www.akeebabackup.com/documentation/solo/angie-installers.html#angie-common-database'));
?>

<div id="restoration-dialog" style="display: none">
    <div class="akeeba-renderer-fef">
        <h3><?php echo AText::_('DATABASE_HEADER_DBRESTORE') ?></h3>

        <div id="restoration-progress">
            <div class="akeeba-progress">
                <div class="akeeba-progress-fill" id="restoration-progress-bar" style="width:20%;"></div>
                <div class="akeeba-progress-status" id="restoration-progress-bar-text">
                    20%
                </div>
            </div>
            <table width="100%" class="akeeba-table--leftbold--striped">
                <tbody>
                <tr>
                    <td width="50%"><?php echo AText::_('DATABASE_LBL_RESTORED') ?></td>
                    <td>
                        <span id="restoration-lbl-restored"></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo AText::_('DATABASE_LBL_TOTAL') ?></td>
                    <td>
                        <span id="restoration-lbl-total"></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo AText::_('DATABASE_LBL_ETA') ?></td>
                    <td>
                        <span id="restoration-lbl-eta"></span>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="akeeba-block--warning" id="restoration-warnings">
                <h4><?php echo AText::_('DATABASE_HEADER_INPROGRESS_WARNINGS'); ?></h4>
                <p>
				    <?php echo AText::_('DATABASE_MSG_INPROGRESS_WARNINGS'); ?>
                    <br />
                    <code id="restoration-inprogress-log"></code>
                </p>
            </div>
        </div>
        <div id="restoration-success">
            <div class="akeeba-block--success" id="restoration-success-nowarnings">
                <h3><?php echo AText::_('DATABASE_HEADER_SUCCESS'); ?></h3>
            </div>
            <div class="akeeba-block--warning" id="restoration-success-warnings">
                <h4><?php echo AText::_('DATABASE_HEADER_WARNINGS'); ?></h4>
                <p>
				    <?php echo AText::_('DATABASE_MSG_WARNINGS'); ?>
                    <br />
                    <code id="restoration-sql-log"></code>
                </p>
            </div>
            <p>
			    <?php echo AText::_('DATABASE_MSG_SUCCESS'); ?>
            </p>
            <button type="button" onclick="databaseBtnSuccessClick();" class="akeeba-btn--green">
                <span class="akion-arrow-right-c"></span>
			    <?php echo AText::_('DATABASE_BTN_SUCCESS'); ?>
            </button>
        </div>
        <div id="restoration-error">
            <div class="akeeba-block--failure">
			    <?php echo AText::_('DATABASE_HEADER_ERROR'); ?>
            </div>
            <div class="well well-small" id="restoration-lbl-error">

            </div>
        </div>
    </div>
</div>

<div>
    <button class="akeeba-btn--dark" style="float: right;" onclick="toggleHelp(); return false;">
        <span class="akion-help"></span>
        Show / hide help
    </button>
    <h1><?php echo $header ?></h1>
</div>

<div class="akeeba-container--50-50">
	<div class="akeeba-panel--teal" style="margin-top: 0">
        <header class="akeeba-block-header">
            <h3><?php echo AText::_('DATABASE_HEADER_CONNECTION');?></h3>
        </header>


		<?php if ($this->large_tables):?>
            <p class="akeeba-block--warning">
				<?php echo AText::sprintf('DATABASE_WARN_LARGE_COLUMNS', $this->large_tables, floor($this->large_tables) + 1)?>
            </p>
		<?php endif;?>

		<div class="AKEEBA_MASTER_FORM_STYLING akeeba-form--horizontal">
			<div class="akeeba-form-group">
				<label for="dbtype">
					<?php echo AText::_('DATABASE_LBL_TYPE') ?>
				</label>
				<?php echo AngieHelperSelect::dbtype($this->db->dbtype, $this->db->dbtech) ?>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_TYPE_HELP') ?>
                </span>
			</div>
			<div class="akeeba-form-group">
				<label for="dbhost">
					<?php echo AText::_('DATABASE_LBL_HOSTNAME') ?>
				</label>
                <input type="text" id="dbhost" placeholder="<?php echo AText::_('DATABASE_LBL_HOSTNAME') ?>" value="<?php echo $this->db->dbhost ?>" />
                <span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('DATABASE_LBL_HOSTNAME_HELP') ?>
				</span>
			</div>
			<div class="akeeba-form-group">
				<label for="dbuser">
					<?php echo AText::_('DATABASE_LBL_USERNAME') ?>
				</label>
                <input type="text" id="dbuser" placeholder="<?php echo AText::_('DATABASE_LBL_USERNAME') ?>" value="<?php echo $this->db->dbuser ?>" />
                <span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('DATABASE_LBL_USERNAME_HELP') ?>
				</span>
			</div>
			<div class="akeeba-form-group">
				<label class="control-label" for="dbpass">
					<?php echo AText::_('DATABASE_LBL_PASSWORD') ?>
				</label>
                <input type="password" id="dbpass" placeholder="<?php echo AText::_('DATABASE_LBL_PASSWORD') ?>" value="<?php echo $this->db->dbpass ?>" />
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_PASSWORD_HELP') ?>
                </span>
			</div>
			<div class="akeeba-form-group">
				<label class="control-label" for="dbname">
					<?php echo AText::_('DATABASE_LBL_DBNAME') ?>
				</label>
                <input type="text" id="dbname" placeholder="<?php echo AText::_('DATABASE_LBL_DBNAME') ?>" value="<?php echo $this->db->dbname ?>" />
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_DBNAME_HELP') ?>
                </span>
			</div>

            <div class="akeeba-form-group">
                <label for="prefix">
					<?php echo AText::_('DATABASE_LBL_PREFIX') ?>
                </label>
                <input type="text" id="prefix" placeholder="<?php echo AText::_('DATABASE_LBL_PREFIX') ?>" value="<?php echo $this->db->prefix ?>" />
                <span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('DATABASE_LBL_PREFIX_HELP') ?>
				</span>
            </div>
        </div>
	</div>

	<div id="advancedWrapper" class="akeeba-panel--info" >
        <header class="akeeba-block-header">
            <h3><?php echo AText::_('DATABASE_HEADER_ADVANCED'); ?></h3>
        </header>

		<div class="AKEEBA_MASTER_FORM_STYLING akeeba-form--horizontal">
			<div class="akeeba-form-group">
				<label for="existing">
					<?php echo AText::_('DATABASE_LBL_EXISTING') ?>
				</label>

                <div class="akeeba-toggle">
                    <input id="existing-drop" type="radio" name="existing" value="drop" <?php echo ($this->db->existing == 'drop') ? 'checked="checked"' : '' ?> />
                    <label for="existing-drop" class="red"><?php echo AText::_('DATABASE_LBL_EXISTING_DROP') ?></label>
                    <input id="existing-backup" type="radio" name="existing" value="backup" <?php echo ($this->db->existing == 'backup') ? 'checked="checked"' : '' ?> />
                    <label for="existing-backup" class="green"><?php echo AText::_('DATABASE_LBL_EXISTING_BACKUP') ?></label>
                </div>
				<span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('DATABASE_LBL_EXISTING_HELP') ?>
				</span>
			</div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label for="foreignkey">
                    <input type="checkbox" id="foreignkey" <?php echo $this->db->foreignkey ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_FOREIGNKEY') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_FOREIGNKEY_HELP') ?>
                </span>
			</div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="noautovalue">
                    <input type="checkbox" id="noautovalue" <?php echo $this->db->noautovalue ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_NOAUTOVALUE') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_NOAUTOVALUE_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="replace">
                    <input type="checkbox" id="replace" <?php echo $this->db->replace ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_REPLACE') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_REPLACE_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="utf8db">
                    <input type="checkbox" id="utf8db" <?php echo $this->db->utf8db ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_FORCEUTF8DB') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
    	            <?php echo AText::_('DATABASE_LBL_FORCEUTF8DB_HELP') ?>
                </span>
			</div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="utf8tables">
                    <input type="checkbox" id="utf8tables" <?php echo $this->db->utf8db ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_FORCEUTF8TABLES') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_FORCEUTF8TABLES_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="utf8mb4">
                    <input type="checkbox" id="utf8mb4" <?php echo $this->db->utf8mb4 ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_UTF8MB4DETECT') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_UTF8MB4DETECT_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="break_on_failed_create">
                    <input type="checkbox" id="break_on_failed_create"
			            <?php echo $this->db->break_on_failed_create ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_ON_CREATE_ERROR') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_ON_CREATE_ERROR_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="break_on_failed_insert">
                    <input type="checkbox" id="break_on_failed_insert"
			            <?php echo $this->db->break_on_failed_insert ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_ON_OTHER_ERROR') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_ON_OTHER_ERROR_HELP') ?>
                </span>
            </div>

            <h4><?php echo AText::_('DATABASE_HEADER_FINETUNING') ?></h4>

            <div class="akeeba-block--info">
                <?php echo AText::_('DATABASE_MSG_FINETUNING'); ?>
            </div>

            <div class="akeeba-form-group">
                <label for="maxexectime">
                    <?php echo AText::_('DATABASE_LBL_MAXEXECTIME') ?>
                </label>
                <input class="input-mini" type="text" id="maxexectime" placeholder="<?php echo AText::_('DATABASE_LBL_MAXEXECTIME') ?>" value="<?php echo $this->db->maxexectime ?>" />
	            <span class="akeeba-help-text" style="display: none;">
                    <?php echo AText::_('DATABASE_LBL_MAXEXECTIME_HELP') ?>
                </span>
            </div>
            <div class="akeeba-form-group">
                <label for="throttle">
                    <?php echo AText::_('DATABASE_LBL_THROTTLEMSEC') ?>
                </label>
                <input class="input-mini" type="text" id="throttle" placeholder="<?php echo AText::_('DATABASE_LBL_THROTTLEMSEC') ?>" value="<?php echo $this->db->throttle ?>" />
                <span class="akeeba-help-text" style="display: none;">
                    <?php echo AText::_('DATABASE_LBL_THROTTLEMSEC_HELP') ?>
                </span>
            </div>
		</div>
	</div>
</div>
