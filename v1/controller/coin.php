<?php

require_once('db.php');
require_once('../model/Coin.php');
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

// coin actions

if (array_key_exists("coinid", $_GET)) {

    $coinid = $_GET['coinid'];
    // validate coin ID
    if ($coinid == '' || !is_numeric($coinid)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Coin ID cannot be blank or non-numeric");
        $response->send();
        exit;
    }

    // ********************************
    // Get a single coin
    // ********************************
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, side, value, image FROM tblcoins WHERE id = :coinid');
            $query->bindParam(':coinid', $coinid, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("coin not found");
                $response->send();
                exit;
            }

            // retrieve the coin
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $coin = new coin($row['id'], $row['name'], $row['side'], $row['value'], $row['image']);
                $coinArray[] = $coin->returnCoinAsArray();
            }

            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['coins'] = $coinArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (coinException $ex) {
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
            $response->addMessage("Failed to get coin");
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
// Get all coins
// ********************************

// this is the /coins route
elseif (empty($_GET)) {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, side, value, image FROM tblcoins');
            $query->execute();

            $rowCount = $query->rowCount();
            $coinArray = array();

            // retrieve the coin
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $coin = new coin($row['id'], $row['name'], $row['side'], $row['value'], $row['image']);
                $coinArray[] = $coin->returnCoinAsArray();
            }
 
            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['coins'] = $coinArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (coinException $ex) {
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
            $response->addMessage("Failed to get coins from all coins");
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