<?php 
// Log.php
use Dubture\Monolog\Reader\LogReader;

use \PHPUnit\Framework\Assert;

trait AuthRoutes {

	protected $authCookies;
	protected $creds;


	 /**
     * @Given that I have a :arg1 auth
     */
    public function thatIHaveAAuth($arg1)
    {
    	$this->creds = require __DIR__."/UserCredentials/".$arg1.".php";
    	$this->request    = new \GuzzleHttp\Psr7\Request("POST",  $this->base_url."/auth", $this->headers, json_encode($this->creds));
        $this->response   = $this->client->send($this->request, ["http_errors" => false]);
    }


}