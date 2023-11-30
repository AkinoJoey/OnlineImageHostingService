<?php

use Helpers\DatabaseHelper;

$oldData = DatabaseHelper::getInactiveImageData30Days();

if(!empty($oldData)){
    DatabaseHelper::deleteInactiveImageData30Days();
    array_map('unlink', $oldData);
}
