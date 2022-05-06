<?php

class Response {

    // these underscore variables are the private variables for this class
    private $_success;
    private $_httpStatusCode;
    private $_messages = array();
    private $_data;
    private $_toCache = false;
    private $_responseData = array();

    // setter functions

    public function setSuccess($success) {
        $this->_success = $success;
    }

    public function setHttpStatusCode($httpStatusCode) {
        $this->_httpStatusCode = $httpStatusCode;
    }

    // add a message to the messages array
    public function addMessage($message) {
        $this->_messages[] = $message;
    }

    public function setData($data) {
        $this->_data = $data;
    }

    public function toCache($toCache) {
        $this->_toCache = $toCache;
    }

    // send data back - return a JSON response
    public function send() {

        header('Content-type: application/json;charset=utf-8');

        // caching available at client or not
        if ($this->_toCache == true) {
            header('Cache-control: max-age=60');
        }
        else {
            header('Cache-control: no-cache, no-store');
        }

        // handle errors for invalid data
        if (($this->_success != false && $this->_success != true) || !is_numeric($this->_httpStatusCode)) {
            http_response_code(500); // server error
            $this->_responseData['statusCode'] = 500; // add to JSON response
            $this->_responseData['success'] = false; // add to JSON response
            $this->addMessage("Response creation error"); // add to messages array
            $this->_responseData['messages'] = $this->_messages; // add messages to JSON response
        }
        else {
            // successful response
            http_response_code($this->_httpStatusCode);
            $this->_responseData['statusCode'] = $this->_httpStatusCode; // add the actual status code to the JSON response
            $this->_responseData['success'] = $this->_success; // add to JSON response
            $this->_responseData['messages'] = $this->_messages; // add to JSON response
            $this->_responseData['data'] = $this->_data; // add to JSON response
        }

        // send data to browser
        echo json_encode($this->_responseData);
    }
}