<?php

require_once('db.php');
require_once('../model/Card.php');
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

// card actions

if (array_key_exists("cardid", $_GET)) {

    $cardid = $_GET['cardid'];
    // validate card ID
    if ($cardid == '' || !is_numeric($cardid)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Card ID cannot be blank or non-numeric");
        $response->send();
        exit;
    }

    // ********************************
    // Get a single card
    // ********************************
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, suit, value, image FROM tblcards WHERE id = :cardid');
            $query->bindParam(':cardid', $cardid, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("card not found");
                $response->send();
                exit;
            }

            // retrieve the card
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $card = new card($row['id'], $row['name'], $row['suit'], $row['value'], $row['image']);
                $cardArray[] = $card->returnCardAsArray();
            }

            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['cards'] = $cardArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (cardException $ex) {
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
            $response->addMessage("Failed to get card");
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
// Get all cards
// ********************************

// this is the /cards route
elseif (empty($_GET)) {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, suit, value, image FROM tblcards');
            $query->execute();

            $rowCount = $query->rowCount();
            $cardArray = array();

            // retrieve the cards
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $card = new card($row['id'], $row['name'], $row['suit'], $row['value'], $row['image']);
                $cardArray[] = $card->returnCardAsArray();
            }
 
            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['cards'] = $cardArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (cardException $ex) {
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
            $response->addMessage("Failed to get cards from all cards");
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