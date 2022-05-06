<?php

class TarotException extends Exception { }

class Tarot {

    private $_id;
    private $_name;
    private $_suit;
    private $_description;
    private $_image;

    // construct

    public function __construct($id, $name, $suit, $description, $image) {

        $this->setID($id);
        $this->setName($name);
        $this->setSuit($suit);
        $this->setDescription($description);
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

    public function getDescription() {
        return $this->_description;
    }

    public function getImage() {
        return $this->_image;
    }

    // setters

    public function setID($id) {
        if (($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)) {
            throw new TarotException("Tarot card ID error");
        }

        $this->_id = $id;
    }

    public function setName($name) {
        if (strlen($name) < 0 || strlen($name) > 255) {
            throw new TarotException("Tarot card name error");
        }

        $this->_name = $name;
    }

    public function setSuit($suit) {
        $suitsArray = array("Major", "Swords", "Wands", "Pentacles", "Cups");

        if (($suit !== null) && (in_array($suit, $suitsArray) == false)) {
            throw new TarotException("Tarot card suit error");
        }

        $this->_suit = $suit;
    }

    public function setDescription($description) {
        if ($description == null) {
            throw new TarotException("Tarot card description missing");
        }

        $this->_description = $description;
    }

    public function setImage($image) {
        if ($image == null) {
            throw new TarotException("Tarot card image missing");
        }

        $this->_image = $image;
    }

    // helper function to allow format to be used for JSON response
    public function returnTarotAsArray() {
        $tarot = array();
        $tarot['id'] = $this->getID();
        $tarot['name'] = $this->getName();
        $tarot['suit'] = $this->getSuit();
        $tarot['description'] = $this->getDescription();
        $tarot['image'] = $this->getImage();

        return $tarot;
    }
}