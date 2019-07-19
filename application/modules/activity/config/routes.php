<?php

$route['activity/print/([a-z]+)/(:num)'] = 'activity/print/$1/$2';

$route['(:any)'] = 'none';