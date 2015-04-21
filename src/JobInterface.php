<?php
namespace JobQueue;

interface JobInterface
{

    /**
     * Set up environment for this job
     */
    public function setPid($pid);


    /**
     * Run job
     */
    public function run();
}