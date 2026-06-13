<?php

$addon = rex_addon::get('klxm_elements');
echo rex_view::title($addon->getProperty('name'));
rex_be_controller::includeCurrentPageSubPath();
