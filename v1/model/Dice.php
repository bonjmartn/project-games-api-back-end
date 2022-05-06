<?php

class DiceException extends Exception { }

class Dice {

    private $_id;
    private $_name;
    private $_color;
    private $_value;
    private $_image;

    // construct

    public function __construct($id, $name, $color, $value, $image) {

        $this->setID($id);
        $this->setName($name);
        $this->setColor($color);
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

    public function getColor() {
        return $this->_color;
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
            throw new DiceException("Dice ID error");
        }

        $this->_id = $id;
    }

    public function setName($name) {
        if (strlen($name) < 0 || strlen($name) > 255) {
            throw new DiceException("Dice name error");
        }

        $this->_name = $name;
    }

    public function setColor($color) {
        $colorsArray = array("White", "Black", "Red");

        if (($color !== null) && (in_array($color, $colorsArray) == false)) {
            throw new DiceException("Dice color error");
        }

        $this->_color = $color;
    }

    public function setValue($value) {
        if (($value !== null) && ($value < 1 || $value > 10)) {
            throw new DiceException("Dice value error");
        }

        $this->_value = $value;
    }

    public function setImage($image) {
        if ($image == null) {
            throw new DiceException("Dice image error");
        }

        $this->_image = $image;
    }

    // helper function to allow format to be used for JSON response
    public function returnDiceAsArray() {
        $dice = array();
        $dice['id'] = $this->getID();
        $dice['name'] = $this->getName();
        $dice['color'] = $this->getColor();
        $dice['value'] = $this->getValue();
        $dice['image'] = $this->getImage();

        return $dice;
    }
}