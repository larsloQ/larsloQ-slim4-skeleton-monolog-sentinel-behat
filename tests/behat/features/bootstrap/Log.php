<?php 
// Log.php
use Dubture\Monolog\Reader\LogReader;

use \PHPUnit\Framework\Assert;

trait Log {
	 protected string $logType;

    protected Array $lastLogEntry;
     protected $logPattern =  '/^\[(?P<date>.*)\] (?P<logger>.+?)\.(?P<level>[A-Z]+): (?P<message>.*) \| (?P<context>.*) \| (?P<extra>.*)/m';

    protected $numOfLogEntries;
     protected $logFile;

     /**
     * @Given :arg1 log is turn :arg2
     */
    public function logIsTurn($arg1, $arg2)
    {
        $onOff = $arg2 == "on" ? true : false;
        Assert::assertEquals($this->settings['log'][$arg1]['active'], $onOff);
    }

 
 /**
     * @Given the :arg1 log of today exists
     */
    public function theLogOfTodayExists($arg1)
    {
      $this->logType = $arg1;
      $this->logFile = "log/".$arg1."/".$arg1."-".date("Y-m-d").".log"; 
      $e = file_exists($this->logFile);
      Assert::assertTrue($e);
    }


 /**
     * @Then I remember number of logs
     */
    public function iRememberNumberOfLogs()
    {
        $reader = new LogReader($this->logFile, $this->logPattern);
        $this->numOfLogEntries = $reader->count();
    }


    /**
     * @Then the :arg1 log should contain a new entry
     */
    public function theLogShouldContainANewEntry($arg1)
    {
        sleep(.1); # wait for log entry to be created
        $reader  = new LogReader($this->logFile, $this->logPattern);
        $plusOne = $this->numOfLogEntries+1;
        Assert::assertEquals($reader->count(),$plusOne);
    }


     /**
     * @Then last :arg1 log entry should contain :arg2 at :arg3
     */
    public function lastLogEntryShouldContainAt($arg1, $arg2, $arg3)
    {
       Assert::assertEquals($this->logType, $arg1, "Make sure that $arg1 is the same as used in given background step");
        $reader  = new LogReader($this->logFile, $this->logPattern);
        $this->lastLogEntry = $reader[$reader->count()-2]; // the last entry is supposed to be a new line and therefore be empty
       // Assert::assertEquals($this->lastLogEntry[$arg3], $arg2);
       Assert::assertContains($arg2, $this->lastLogEntry[$arg3]);
      
    }
  
}