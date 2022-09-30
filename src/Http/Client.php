<?php

namespace Http;

class Client
{
  /**
   * Location where the cookie will be stored.
   */
  private $useCookies;


  /**
   * Name use to save cookie file.
   */
  private $cookieName;


  /**
   * Location where the cookie will be stored.
   */
  private $cookieLocation;


  public function __construct($useCookies = false, $cookieName = 'cookie', $cookieLocation = 'tmp')
  {
    $this->useCookies = $useCookies;
    $this->cookieName = $cookieName;
    $this->cookieLocation = $cookieLocation;
  }

  public function get($url)
  {
    $ch = curl_init();

    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_HTTPGET => true
    ]);

    return $this->send($ch);
  }

  public function post($url, $params)
  {
    $ch = curl_init();

    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query($params)
    ]);

    return $this->send($ch);
  }

  private function send($ch)
  {
    if ($this->useCookies) {
      curl_setopt_array($ch, [
        CURLOPT_COOKIEFILE => $this->getCookiePath(),
        CURLOPT_COOKIEJAR => $this->getCookiePath(),
      ]);
    }

    curl_setopt_array($ch, [
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_TCP_KEEPALIVE => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true
    ]);
    if (!is_dir('tmp')) mkdir('tmp');
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }

  public function clearCookie()
  {
    if ($this->useCookies) {
      $filepath = $this->getCookiePath();
      if (is_file($filepath)) unlink($filepath);
    }
  }

  public function getCookiePath()
  {
    if ($this->useCookies) {
      return join('/', [$this->cookieLocation, $this->cookieName]);
    }

    return null;
  }
}
