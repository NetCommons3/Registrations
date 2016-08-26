<?php
echo $this->NetCommonsHtml->script(array(
	'/registrations/js/registrations.js'
));
$maxQuestionWarningMsg = sprintf(
	__d('registrations', 'Number of questions that can be created is up %d . Already it has become %d .'),
	RegistrationsComponent::MAX_QUESTION_COUNT,
	RegistrationsComponent::MAX_QUESTION_COUNT
);
$maxChoiceWarningMsg = sprintf(
	__d('registrations', 'Number of choices that can be created is up %d per question. Already it has become %d .'),
	RegistrationsComponent::MAX_CHOICE_COUNT,
	RegistrationsComponent::MAX_CHOICE_COUNT
);

echo $this->NetCommonsHtml->scriptBlock(
	'NetCommonsApp.constant("registrationsMessages", {' .
	'"newPageLabel": "' . __d('registrations', 'page') . '",' .
	'"newQuestionLabel": "' . __d('registrations', 'New Question') . '",' .
	'"newChoiceLabel": "' . __d('registrations', 'new choice') . '",' .
	'"newChoiceColumnLabel": "' . __d('registrations', 'new column choice') . '",' .
	'"newChoiceOtherLabel": "' . __d('registrations', 'other choice') . '",' .
	'"maxQuestionWarningMsg": "' . $maxQuestionWarningMsg . '",' .
	'"maxChoiceWarningMsg": "' . $maxChoiceWarningMsg . '",' .
	'});'
);

echo $this->NetCommonsHtml->css('/registrations/css/registration.css');

