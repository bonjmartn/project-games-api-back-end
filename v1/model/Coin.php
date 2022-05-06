<?php

class CoinException extends Exception { }

class Coin {

    private $_id;
    private $_name;
    private $_side;
    private $_value;
    private $_image;

    // construct

    public function __construct($id, $name, $side, $value, $image) {

        $this->setID($id);
        $this->setName($name);
        $this->setSide($side);
        $this->setValue($value);
        $this->setImage($image);
    }

    // getters

    public function getID() {
        return $this->_id;
    }

    public function getName() {
        return $this->_name;
    }

    public function getSide() {
        return $this->_side;
    }

    public function getValue() {
        return $this->_value;
    }

    public function getImage() {
        return $this->_image;
    }

    // setters

    public function setID($id) {
        if (($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)) {
            throw new CoinException("Coin ID error");
        }

        $this->_id = $id;
    }

    public function setName($name) {
        if (strlen($name) < 0 || strlen($name) > 255) {
            throw new CoinException("Coin name error");
        }

        $this->_name = $name;
    }

    public function setSide($side) {
        $sidesArray = array("Heads", "Tails");

        if (($side !== null) && (in_array($side, $sidesArray) == false)) {
            throw new CoinException("Coin side error");
        }

        $this->_side = $side;
    }

    public function setValue($value) {
        if (($value !== null) && ($value < 1 || $value > 50)) {
            throw new CoinException("Coin value error");
        }

        $this->_value = $value;
    }

    public function setImage($image) {
        if ($image == null) {
            throw new CoinException("Coin image error");
        }

        $this->_image = $image;
    }

    // helper function to allow format to be used for JSON response
    public function returnCoinAsArray() {
        $coin = array();
        $coin['id'] = $this->getID();
        $coin['name'] = $this->getName();
        $coin['side'] = $this->getSide();
        $coin['value'] = $this->getValue();
        $coin['image'] = $this->getImage();

        return $coin;
    }
}