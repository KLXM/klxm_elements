<?php

$addon = rex_addon::get('klxm_elements');

if (!rex::getUser() || !rex::getUser()->isAdmin()) {
	echo rex_view::error('Nur Administratoren dürfen diese Seite aufrufen.');
	return;
}

echo rex_view::title($addon->getProperty('name'));
rex_be_controller::includeCurrentPageSubPath();
