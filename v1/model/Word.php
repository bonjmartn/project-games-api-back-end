<?php

class WordException extends Exception { }

class Word {

    private $_id;
    private $_word;
    private $_num_chars;
    private $_topic;

    // construct

    public function __construct($id, $word, $num_chars, $topic) {

        $this->setID($id);
        $this->setWord($word);
        $this->setNum_Chars($num_chars);
        $this->setTopic($topic);
    }

    // getters

    public function getID() {
        return $this->_id;
    }

    public function getWord() {
        return $this->_word;
    }

    public function getNum_Chars() {
        return $this->_num_chars;
    }

    public function getTopic() {
        return $this->_topic;
    }

    // setters

    public function setID($id) {
        if (($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)) {
            throw new WordException("Word ID error");
        }

        $this->_id = $id;
    }

    public function setWord($word) {
        if (strlen($word) < 0 || strlen($word) > 255) {
            throw new WordException("Word text error");
        }

        $this->_word = $word;
    }

    public function setNum_Chars($num_chars) {
        if (($num_chars !== null) && ($num_chars < 1 || $num_chars > 255)) {
            throw new WordException("Word number of characters error");
        }

        $this->_num_chars = $num_chars;
    }

    public function setTopic($topic) {

        $topicsArray = array("Months", "Games", "States");

        if (($topic !== null) && (in_array($topic, $topicsArray) == false)) {
            throw new WordException("Word topic error");
        }

        $this->_topic = $topic;
    }

    public function setImage($image) {
        if ($image == null) {
            throw new WordException("Word image error");
        }

        $this->_image = $image;
    }

    // helper function to allow format to be used for JSON response
    public function returnWordAsArray() {
        $word = array();
        $word['id'] = $this->getID();
        $word['word'] = $this->getWord();
        $word['num_chars'] = $this->getNum_Chars();
        $word['topic'] = $this->getTopic();

        return $word;
    }
}