<?php
namespace JobDaemon;

use Psr\Log\NullLogger;

class Job
{

    /**
     * 
     * @var integer PID of this process
     */
    private $pid;

    /**
     *
     * @var NullLogger
     */
    private $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }


    public function setPid($pid)
    {
        $pid = (integer) $pid;
        $this->pid = $pid;
    }

    public function run()
    {
        $sleep = rand(1,5);
        $this->logger->debug('sleeping: '.$sleep);
        
        sleep($sleep);


    }

}