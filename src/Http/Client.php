<?php

namespace Http;

class Client
{
  /**
   * Location where the cookie will be stored.
   */
  private $cookie = 'tmp/cookie.txt';

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
    curl_setopt_array($ch, [
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_COOKIEFILE => $this->cookie,
      CURLOPT_COOKIEJAR => $this->cookie,
      CURLOPT_TCP_KEEPALIVE => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true
    ]);
    if (!is_dir('tmp')) mkdir('tmp');
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}
