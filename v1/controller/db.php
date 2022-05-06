<?php

class DB {

    //private static $writeDBConnection;
    private static $readDBConnection;

    // public static function connectWriteDB() {
    //     if (self::$writeDBConnection === null) {
    //         //self::$writeDBConnection = new PDO('mysql:host=mysql.api.projectgamesapi.xyz;dbname=projectgamesapi_tasksdb;charset=utf8', 'f9fpd3y5', 'xe9yk2s8h5dapjmr');
    //         self::$writeDBConnection = new PDO('mysql:host=localhost;dbname=tasksdb;charset=utf8', 'root', 'root');
    //         self::$writeDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //         self::$writeDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //     }
    //     return self::$writeDBConnection;
    // }

    public static function connectReadDB() {
        if (self::$readDBConnection === null) {
            self::$readDBConnection = new PDO('mysql:host=mysql.api.projectgamesapi.xyz;dbname=projectgamesapi;charset=utf8', 'f9fpd3y5', 'xe9yk2s8h5dapjmr');
            //self::$readDBConnection = new PDO('mysql:host=localhost;dbname=projectgamesapi;charset=utf8', 'root', 'root');
            self::$readDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$readDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        return self::$readDBConnection;
    }
}