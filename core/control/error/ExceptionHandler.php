<?php

namespace core\control\error;


use core\control\System;
use core\datatypes\ArrayList;
use core\datatypes\JsonObject;
use core\view\html\GenericElement;

/**
 * The class ExceptionHandler is responsble for dealing with the display of the exceptions
 * @package core\control\error
 */
class ExceptionHandler {

    /**
     * The Error
     * @var \ErrorException
     */
    private $exception;

    /**
     * Error showing screen layout
     * @var GenericElement
     */
    private $errorScreen;

    /**
     * Fatal error flag
     * @var bool
     */
    private $isFatal = false;

    /**
     * Defines the error to be handled
     * @param \ErrorException $e
     * @return void
     */
    public function setError(\ErrorException $e){
        $this->exception = $e;
    }

    /**
     * Sets the template to be loaded and display the error
     * @param string $template
     * @param string $path
     * @return void
     */
    public function setErrorTemplate($template, $path){
        $tmp = new ArrayList();     // workaround to import the classes needed for show the error
        $tmp = new JsonObject();    // as Json. TODO: fix it ASAP, it's too ugly
        $this->errorScreen = GenericElement::layoutInflater($template,$path);
    }

    /**
     * Returns the stack trace as array
     * @param \ErrorException $e
     * @return array
     */
    public static function getStack(\ErrorException $e){
        $error = explode('#', $e->getTraceAsString());
        array_shift($error);
        return $error;
    }

    /**
     * Turns On the fatal error flag
     * @return void
     */
    public function isFatalError(){
        $this->isFatal = true;
    }

    /**
     * Renders the error data
     * @return string
     */
    public function display(){
        if(!is_null($this->exception)){
            $config = System::getConfig();
            $errorMode = (isset($config->errorMode))? $config->errorMode : null;
            switch($errorMode){
                case 'JSON':
                    $response = $this->displayAsJson($this->exception, $this->isFatal, $config);
                    break;
                case 'HTML':
                default:
                    $response = $this->displayAsHtml($this->exception, $this->isFatal, $config);
                    break;
            }
            System::display($response);
        }

    }

    /**
     * Returns the error screen data as HTML
     * @param \ErrorException $e Thrown exception object
     * @param int $fatal Greater than zero if it was a fatal error (default: 0)
     * @return string
     */
    private function displayAsHtml(\ErrorException $e, $fatal, $config){
        $layout = $this->errorScreen;
        if(!$config->debugMode){
            $end_msg = 'An error ocurred. Please, contact the administrator: '.$config->emailAdmin;
            $layout->removeElementById('ERROR_MESSAGE');
            $layout->removeElementById('stackTrace');
        }
        else{
            $end_msg = ($fatal)? 'A fatal error ocurred.':
                'An error ocurred in the system.';
            $layout->getElementById('ERROR_MESSAGE')->add(GenericElement::stringInflater("<p>".get_class($e)." ".$e->getCode().": ".$e->getMessage()."</p>"));
            $layout->getElementById('ERROR_MESSAGE')->add(GenericElement::stringInflater("<p>Error ocurred in <strong>line ".$e->getLine()."</strong> of the file <strong>".$e->getFile()."</strong></p>"));

            foreach(self::getStack($e) as $stackline)
                $layout->getElementById('stackTrace')->add(
                    GenericElement::stringInflater("<p>{$stackline}</p>")
                );
        }
        $layout->getElementById('ERROR_TYPE')->getElement(0)->add(
            GenericElement::stringInflater("<p>{$end_msg}</p>")
        );
        return $layout;
    }

    private function displayAsJson(\ErrorException $e, $fatal, $config){
        $response = new JsonObject();
        if(!$config->debugMode){
            $response->setKey("type", "ErrorException");
            $response->setKey("message", 'An error ocurred. Please, contact the administrator: '.$config->emailAdmin);
        } else {
            $response->setKey("type", get_class($e));
            $response->setKey("code", $e->getCode());
            $response->setKey("message", $e->getMessage());
            $response->setKey("file", $e->getFile());
            $response->setKey("line", $e->getLine());
            $stackTrace = new ArrayList();
            $response->setKey("stacktrace", $stackTrace);

            foreach(self::getStack($e) as $stackline){
                $stackTrace->push($stackline);
            }
        }
        return $response;
    }

    /**
     * Writes the exception information to the log file
     *
     * @param int $errorNumber Error number
     * @param string $errorType Error type
     * @param string $errorMsg Error message
     * @param string $file Error file
     * @param int $line Error line
     * @return void
     */
    public static function writeLog($errorType,$errorNumber,$errorMsg,$file,$line){
        date_default_timezone_set("America/Los_Angeles");
        file_put_contents(System::getConfig()->logPath,"[".date("c")."] {$errorType} ERROR {$errorNumber}: {$errorMsg} in {$file}({$line})\n",FILE_APPEND);
    }

}