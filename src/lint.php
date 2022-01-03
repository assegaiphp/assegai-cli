#!/usr/bin/env php
<?php

require_once 'bootstrap.php';

echo shell_exec("$workingDirectory/vendor/bin/phplint $workingDirectory/ --exclude=vendor");