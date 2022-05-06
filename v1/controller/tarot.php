<?php

require_once('db.php');
require_once('../model/Tarot.php');
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

// tarot card actions

if (array_key_exists("tarotid", $_GET)) {

    $tarotid = $_GET['tarotid'];
    // validate tarot card ID
    if ($tarotid == '' || !is_numeric($tarotid)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Card ID cannot be blank or non-numeric");
        $response->send();
        exit;
    }

    // ********************************
    // Get a single tarot card
    // ********************************
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, suit, description, image FROM tbltarot WHERE id = :tarotid');
            $query->bindParam(':tarotid', $tarotid, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Tarot card not found");
                $response->send();
                exit;
            }

            // retrieve the tarot card
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tarot = new tarot($row['id'], $row['name'], $row['suit'], $row['description'], $row['image']);
                $tarotArray[] = $tarot->returnTarotAsArray();
            }

            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['tarot'] = $tarotArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (tarotException $ex) {
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
            $response->addMessage("Failed to get tarot card");
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
// Get all tarot cards
// ********************************

// this is the /tarot route
elseif (empty($_GET)) {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, suit, description, image FROM tbltarot');
            $query->execute();

            $rowCount = $query->rowCount();
            $tarotArray = array();

            // retrieve the tarot cards
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tarot = new tarot($row['id'], $row['name'], $row['suit'], $row['description'], $row['image']);
                $tarotArray[] = $tarot->returnTarotAsArray();
            }
 
            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['tarot'] = $tarotArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (tarotException $ex) {
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
            $response->addMessage("Failed to get tarot cards from all tarot cards");
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