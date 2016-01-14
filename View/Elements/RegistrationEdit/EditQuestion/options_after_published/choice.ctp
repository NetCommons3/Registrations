<?php
/**
 * registration page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
{{choice.choiceLabel}}
<span ng-if="choice.otherChoiceType != <?php echo RegistrationsComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
    <?php echo __d('registrations', '(This is [other] choice. Area to enter the text is automatically granted at the time of implementation.)'); ?>
</span>