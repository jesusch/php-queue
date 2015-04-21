<?php
namespace JobQueue;

interface JobInterface
{

    /**
     * Sets the current PID
     */
    public function setPid($pid);


    /**
     * Runs the real job code
     */
    public function run();
}