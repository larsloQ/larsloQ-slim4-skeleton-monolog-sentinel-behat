<?php

use Behat\Behat\Context\Context;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use \PHPUnit\Framework\Assert;

require 'vendor/autoload.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    use AuthRoutes;
    use Log;

    /**
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \GuzzleHttp\Psr7\Request
     */
    protected $request;
    protected $headers;

    /**
     * @var GuzzleHttp\Psr7\Response
     */
    protected $response;
    protected $contents;
    protected $statusCode;

    /* keep track of files produces while testing to clean up after tests*/
    protected $files = [];

    /* a duplicate of currently used file*/
    protected $dupe;

    /**
     * @var string
     */
    protected $base_url;

    protected $settings;

    /**
     * Constructs a new instance.
     *
     * @param      string  $baseUrl  passed by behat.yml
     */
    public function __construct(string $baseUrl)
    {

        /* have the same settings than app */
        $boot           = require_once __DIR__ . '/../../../../app/setup_container.php';
        $this->settings = $this->settings == null ? get_settings() : $this->settings;

        $this->headers = ["content-type" => 'application/json'];
        $this->output  = new \Symfony\Component\Console\Output\BufferedOutput();
        /* init Guzzle HTTP Client*/
        $this->base_url = $baseUrl;
        $this->client   = new GuzzleHttp\Client([
            'base_uri' => $this->base_url,
            "cookies"  => true, // keep cookies for all requests in client !!!!
        ]);

    }

    /**
     * @When I request :arg1 with :arg2
     *
     */
    public function iRequestWith($arg1, $arg2)
    {
        $headers = array("headers" => $this->headers);
        $uri     = $this->base_url . $arg1;
        echo "Requesting : $uri $arg1 " . "\n";
        $this->request    = new \GuzzleHttp\Psr7\Request($arg2, $uri, $this->headers);
        $this->response   = $this->client->send($this->request, ["http_errors" => false]);
        $this->statusCode = $this->response->getStatusCode();
        $this->contents   = $this->response->getBody()->getContents();
    }

    /**
     * @Then the response status code should be :arg1
     */
    public function theResponseStatusCodeShouldBe($arg1)
    {
        // var_dump($this->statusCode);
        Assert::assertEquals(
            intval($arg1),
            $this->statusCode,
            "Debug: " . $this->statusCode . ' Content: ' . $this->contents
        );
    }

    /**
     * @Then I want to see the response
     */
    public function iWantToSeeTheResponse()
    {
        print_r($this->contents);
    }

    /**
     * @Then response should contain :arg1
     */
    public function responseShouldContain($arg1)
    {
        Assert::assertContains($arg1, $this->contents);
    }

    /**
     * @When I post with payload :arg1 to :arg2
     */
    public function iPostWithPayloadTo($arg1, $arg2)
    {
        $this->payload    = require __DIR__ . "/Payloads/" . $arg1 . ".php";
        $this->request    = new \GuzzleHttp\Psr7\Request("POST", $this->base_url . $arg2, $this->headers, json_encode($this->payload));
        $this->response   = $this->client->send($this->request, ["http_errors" => false]);
        $this->statusCode = $this->response->getStatusCode();
        $this->contents   = $this->response->getBody()->getContents();
         if ($arg2=="/backend/save") {
            if (stripos($this->contents, "filename")>-1) {
                $json = json_decode($this->contents);
                $this->files[] = $this->settings['general_settings']['data_repo_folder']."/".$json->filename;
            
            }
         }

    }


    /**
     * @When I duplicate currently used file into repo
    */
    public function iDuplicateCurrentlyUsedFileIntoRepo()
    {
        $data_folder = $this->settings['general_settings']['data_folder'];
        $repo = $this->settings['general_settings']['data_repo_folder'];
        $file = $this->settings['general_settings']['data_file'];
        $current = $data_folder."/".$file;
        $timeCode = date("Y-m-d-H-i");
        $dest = $repo."/".$file."-".$timeCode.".json";
        copy($current.".json", $dest);
        $this->files[] = $dest;
        $this->dupe = $file."-".$timeCode.".json";
       
    }


     /**
     * @When use dupe to setAsCurrent
     */
    public function useDupeToSetascurrent()
    {
        $payload = [
            "fileId"=> $this->dupe,
        ];
        $this->request    = new \GuzzleHttp\Psr7\Request("POST", $this->base_url . "/backend/setcurrent", $this->headers, json_encode($payload));
        $this->response   = $this->client->send($this->request, ["http_errors" => false]);
        $this->statusCode = $this->response->getStatusCode();
        $this->contents   = $this->response->getBody()->getContents();
    }



/**
 * Cleans up files after every scenario.
 *
 * @AfterScenario @fileproduced, @rewind
 */
    public function cleanUpFiles($event)
    {
        // // Delete each file in the array.
        foreach ($this->files as $file_path) {
          unlink($file_path);
        }
    }



}
