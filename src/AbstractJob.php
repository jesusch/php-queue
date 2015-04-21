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
    
    /**
     * (non-PHPdoc)
     * @see \JobQueue\JobInterface::setPid()
     */
    public function setPid($pid)
    {
        $pid = (integer) $pid;
        $this->pid = $pid;
    }
    

    /**
     * (non-PHPdoc)
     * @see \JobQueue\JobInterface::run()
     */
    public function run() {}

    
}