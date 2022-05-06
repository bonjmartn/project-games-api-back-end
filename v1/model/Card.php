<?php

class CardException extends Exception { }

class Card {

    private $_id;
    private $_name;
    private $_suit;
    private $_value;
    private $_image;

    // construct

    public function __construct($id, $name, $suit, $value, $image) {

        $this->setID($id);
        $this->setName($name);
        $this->setSuit($suit);
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

    public function getSuit() {
        return $this->_suit;
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
            throw new CardException("Card ID error");
        }

        $this->_id = $id;
    }

    public function setName($name) {
        if (strlen($name) < 0 || strlen($name) > 255) {
            throw new CardException("Card name error");
        }

        $this->_name = $name;
    }

    public function setSuit($suit) {
        $suitsArray = array("Spades", "Hearts", "Clubs", "Diamonds");

        if (($suit !== null) && (in_array($suit, $suitsArray) == false)) {
            throw new CardException("Card suit error");
        }

        $this->_suit = $suit;
    }

    public function setValue($value) {
        if (($value !== null) && ($value < 1 || $value > 10)) {
            throw new CardException("Card value error");
        }

        $this->_value = $value;
    }

    public function setImage($image) {
        if ($image == null) {
            throw new CardException("Card image error");
        }

        $this->_image = $image;
    }

    // helper function to allow format to be used for JSON response
    public function returnCardAsArray() {
        $card = array();
        $card['id'] = $this->getID();
        $card['name'] = $this->getName();
        $card['suit'] = $this->getSuit();
        $card['value'] = $this->getValue();
        $card['image'] = $this->getImage();

        return $card;
    }
}