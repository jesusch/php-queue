<?php

use JobQueue\Queue;
use JobQueue\Job;

// num of jobs to create
$jobs = 100;

define ('BASEPATH',  dirname(__DIR__));
require_once BASEPATH . '/vendor/autoload.php';




$logger = Logger::getLogger('main');
$logger->configure(BASEPATH . '/test/log4php.xml');
$queue = new Queue($logger);



for ($i=0; $i < $jobs;$i++) {
    $logger->debug("creating job: " . $i);
    $job = new Job($logger);
    
    $queue->appendJob($job);
}

$queue->waitForJobs();