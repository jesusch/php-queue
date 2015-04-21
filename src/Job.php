<?php
namespace JobQueue;

class Job extends AbstractJob
{



    public function run()
    {
        $sleep = rand(1,5);
        $this->logger->debug('sleeping: '.$sleep);
        
        sleep($sleep);

    }


}