<?php
require_once  'classes/authorization.class.php';

$logout = new Authorization();

$logout->logout();

echo '<meta http-equiv=refresh content="0; URL=index.php">';