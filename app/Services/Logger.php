<?php

namespace App\Services;

use Monolog\Logger as Monologger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;


final class Logger extends Monologger implements LoggerInterface 
{

    protected $monologger;
    /**
     * Logger constructor.
     * @param array $settings Settings
     */
    
    public function __construct(Array $settings = []) 
    {
        $this->monologger = new Monologger($settings['name']);
        $timeFormat = \DateTimeInterface::W3C;

        // the default output format is "[%datetime%] %channel%.%level_name%: %message% | %context% | %extra%\n"
            // "[%datetime%] [%level_name%]  %message% %context%\n\n",
         $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% | %context% | %extra%\n",
            $timeFormat,
            false, // no inline linebreaks
            true // ignoreEmptyContextAndExtra
        );
        /* Log to timestamped files */
        $rotating = new RotatingFileHandler($settings['path'], 0, Monologger::DEBUG);
        $rotating->setFormatter($formatter);
        $this->monologger->pushProcessor(new WebProcessor());
        $this->monologger->pushHandler($rotating);
    }

    public function getLogger() : LoggerInterface
    {
        return $this->monologger;
    }
}
