<?php

use Psr\Log\NullLogger;

namespace JobQueue;

abstract class AbstractJob implements JobInterface
{
    
    /**
     *
     * @var integer PID of this process
     */
    protected $pid;
    
    /**
     *
     * @var NullLogger
     */
    protected  $logger;
    
    public function __construct($logger)
    {
        $this->logger = $logger;
    }
    
    public function setPid($pid)
    {
        $pid = (integer) $pid;
        $this->pid = $pid;
    }
    
    
    /**
     * Run job
    */
    public function run() {}

    
}