<?php

require_once('db.php');
require_once('../model/Word.php');
require_once('../model/Response.php');

// connect to database
try {
    $readDB = DB::connectReadDB();
}
catch (PDOException $ex) {
    erorr_log("Connection error - ".$ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database conection error");
    $response->send();
    exit;
}

// handle options request method for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400');
    $response = new Response();
    $response->setHttpStatusCode(200);
    $response->setSuccess(true);
    $response->send();
}

// word actions

if (array_key_exists("wordid", $_GET)) {

    $wordid = $_GET['wordid'];
    // validate word ID
    if ($wordid == '' || !is_numeric($wordid)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Word ID cannot be blank or non-numeric");
        $response->send();
        exit;
    }

    // ********************************
    // Get a single word
    // ********************************
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, word, num_chars, topic FROM tblwords WHERE id = :wordid');
            $query->bindParam(':wordid', $wordid, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Word not found");
                $response->send();
                exit;
            }

            // retrieve the word
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $word = new Word($row['id'], $row['word'], $row['num_chars'], $row['topic']);
                $wordArray[] = $word->returnWordAsArray();
            }

            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['words'] = $wordArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (WordException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }
        catch (PDOException $ex) {
            error_log("Database query error - ".$ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to get word");
            $response->send();
            exit;
        }
    }    
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
}
// ********************************
// Get words by topic
// ********************************
elseif (array_key_exists("topic", $_GET)) {

    $topic = $_GET['topic'];

    if ($topic !== 'Months' && $topic !== 'Games' && $topic !== 'States') {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Topic filter must be Months, Games, or States");
        $response->send();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, word, num_chars, topic FROM tblwords WHERE topic = :topic');
            $query->bindParam(':topic', $topic, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();
            $wordArray = array();

            // retrieve the word
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               $word = new Word($row['id'], $row['word'], $row['num_chars'], $row['topic']);
               $wordArray[] = $word->returnWordAsArray();
            }

            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['words'] = $wordArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (WordException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }
        catch (PDOException $ex) {
            error_log("Database query error - ".$ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to get word from topic filter");
            $response->send();
            exit;
        }
    }
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
}
// ********************************
// Get all words
// ********************************

// this is the /words route
elseif (empty($_GET)) {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, word, num_chars, topic FROM tblwords');
            $query->execute();

            $rowCount = $query->rowCount();
            $wordArray = array();

            // retrieve the word
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $word = new Word($row['id'], $row['word'], $row['num_chars'], $row['topic']);
                $wordArray[] = $word->returnWordAsArray();
            }
 
            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['words'] = $wordArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (WordException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }
        catch (PDOException $ex) {
            error_log("Database query error - ".$ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to get word from all words");
            $response->send();
            exit;
        }
    }    
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
}
// ********************************
// Endpoint wasn't found
// ********************************
else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Endpoint not found");
    $response->send();
    exit;
}