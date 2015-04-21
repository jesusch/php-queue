<?php

namespace JobQueue;

use Psr\Log\NullLogger;
declare(ticks=1);


/**
 * Class that handles external Job forks
 *
 * based on JobDaemon
 * http://php.net/manual/de/function.pcntl-fork.php
 * by duerra at yahoo dot com
 * @author jesusch
 *
 */
class Queue{

    /**
     * 
     * @var number of concurrent running jobs
     */
    private $maxProcesses;
    protected $jobsStarted = 0;
    protected $currentJobs = array();
    protected $signalQueue=array();
    protected $parentPID;
    
    /**
     * 
     * @var Psr\Log\NullLogger
     */
    private $logger;


    /**
     * 
     * @param number $maxProcs
     * @param \Psr\Log\NullLogger $logger
     */
    public function __construct($logger=null, $maxProcs=10) {
        
        $this->maxProcesses = (int) $maxProcs;
        $this->logger = $logger;
        $this->parentPID = getmypid();
        pcntl_signal(SIGCHLD, array($this, "childSignalHandler"));
    }
    
    
    /**
     * Set the number of maxprocesses
     * @param number $maxProcs
     */
    public function setMaxProcs($maxProcs)
    {
        $this->maxProcesses = (int) $maxProcs;
    }


    /**
     * waits for all queued jobs
     */
    public function waitForJobs()
    {
        //Wait for child processes to finish before exiting here
        while(count($this->currentJobs)) {
            $this->logger->debug("Waiting for current jobs to finish: ". count($this->currentJobs));
            sleep(1);
        }
    }

    /**
     * append a new Job
     * @param JobQueue\Job $job
     */
    public function appendJob(JobInterface $job)
    {
        $jobID = rand(0,10000000000000);

        while(count($this->currentJobs) >= $this->maxProcesses){
            $this->logger->debug("Maximum children allowed, waiting...");
            sleep(1);
        }

        $pid = pcntl_fork();
        if($pid == -1){
            //Problem launching the job
            $this->logger->critical('Could not launch new job, exiting');
            return false;
        }
        else if ($pid){
            // Parent process
            // Sometimes you can receive a signal to the childSignalHandler function before this code executes if
            // the child script executes quickly enough!
            //
            $this->currentJobs[$pid] = $jobID;

            // In the event that a signal for this pid was caught before we get here, it will be in our signalQueue array
            // So let's go ahead and process it now as if we'd just received the signal
            if(isset($this->signalQueue[$pid])){
                $this->logger->debug("found $pid in the signal queue, processing it now");
                $this->childSignalHandler(SIGCHLD, $pid, $this->signalQueue[$pid]);
                unset($this->signalQueue[$pid]);
            }
        }
        else{
            //Forked child, do your deeds....
            $exitStatus = 0; //Error code if you need to or whatever
            $job->setPid(getmypid());
            $job->run();
            exit($exitStatus);
        }
        return true;
    }

    protected function childSignalHandler($signo, $pid=null, $status=null){

        //If no pid is provided, that means we're getting the signal from the system.  Let's figure out
        //which child process ended
        if(!$pid){
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }

        //Make sure we get all of the exited children
        while($pid > 0){

            if($pid && isset($this->currentJobs[$pid])){
                $exitCode = pcntl_wexitstatus($status);
                if($exitCode != 0){
                    $this->logger->info("$pid exited with status ".$exitCode);
                }
                unset($this->currentJobs[$pid]);
            }
            else if($pid){
                //Oh no, our job has finished before this parent process could even note that it had been launched!
                //Let's make note of it and handle it when the parent process is ready for it
                $this->logger->info("..... Adding $pid to the signal queue .....");
                $this->signalQueue[$pid] = $status;
            }
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }
        return true;
    }

}