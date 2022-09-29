<?php

namespace Core\Crawler;

use Http\Client;
use Core\Spider;

class PR extends Spider
{
  public function __construct()
  {
    parent::__construct(
      new Client(),
      'http://www.sintegra.fazenda.pr.gov.br/sintegra/',
      'http://www.sintegra.fazenda.pr.gov.br/sintegra/captcha?1',
      'data[Sintegra1][CodImage]',
      'data[Sintegra1][Cnpj]',
      ['empresa' => 'Consultar Empresa']);
  }

  public function plugIn()
  {
    $this->httpClient->get($this->siteURL);
  }

  public function hasCaptcha()
  {
    return !empty($this->captchaURL);
  }

  public function loadCaptcha()
  {
    $captcha = $this->httpClient->get($this->captchaURL);
    if (!is_dir('tmp')) mkdir('tmp');
    file_put_contents('tmp/captcha.jpeg', $captcha);
  }

  public function postInfo($params = [])
  {
    $result = $this->httpClient->post($this->siteURL, $params);
    return utf8_encode($result);
  }

  public function parseResults($content)
  {
    $dom = new \DOMDocument('1.0','UTF-8');
    $dom->loadHTML($content);   
    $finder = new \DOMXPath($dom);
    $classname = 'form_conteudo';
    $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

    var_dump(count($nodes));

    return '';
  }
}
