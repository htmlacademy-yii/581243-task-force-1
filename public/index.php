<?php

use TaskForce\classes\Task;

require_once '../vendor/autoload.php';

$task = new Task(1);

var_dump($task->getAllStatuses());
var_dump($task->getAllActions());
