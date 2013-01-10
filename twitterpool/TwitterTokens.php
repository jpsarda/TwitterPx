<?php
require_once '../tmhOAuth/tmhOAuth.php';
require_once '../tmhOAuth/tmhUtilities.php';

class TwitterTokens { 
  private $tokens = false; 
  function saveTokens() {
    if ($this->tokens!==false) {
      $file=dirname(__FILE__)."/twitterpooltokens.bin";
      $tosave=serialize($this->tokens);
      file_put_contents($file,$tosave);
    }
  }
  function getTokens() {
    if ($this->tokens===false) {
      $file=dirname(__FILE__)."/twitterpooltokens.bin";
      $saved=@file_get_contents($file);
      if (!$saved) $this->tokens=array();
      else $this->tokens=unserialize($saved);
    }
    return $this->tokens;
  }
  function addToken($screenname,$token,$secret) {
    $this->getTokens();
    $this->tokens[$screenname]=array("token"=>$token,"secret"=>$secret);
  }
  function removeToken($screenname) {
    $this->getTokens();
    unset($this->tokens[$screenname]);
  }
  function getRandomToken() {
    $this->getTokens();
    $key = array_rand($this->tokens);
    $value = $this->tokens[$key];
    return $value;
  }
  function getAuth($screenname) {
    $this->getTokens();
    return $this->tokens[$screenname];
  }
}
?>