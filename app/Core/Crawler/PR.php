<?php

namespace App\Core\Crawler;

use App\Http\Client as HttpClient;
use App\Core\Spider;

class PR extends Spider
{
  public function __construct()
  {
    parent::__construct(
      new HttpClient(true, 'sintegra.pr', 'tmp'),
      'http://www.sintegra.fazenda.pr.gov.br/sintegra/',
      'http://www.sintegra.fazenda.pr.gov.br/sintegra/captcha?1',
      'data[Sintegra1][CodImage]',
      'data[Sintegra1][Cnpj]',
      ['empresa' => 'Consultar Empresa'],
      'form_conteudo',
      'erro_msg_custom'
    );
  }

  public function generateCookie()
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

  public function getCompanyInfo()
  {
    $params = $this->getPostInfo();
    $result = $this->httpClient->post($this->siteURL, $params);
    $this->content = html_entity_decode($result);
  }

  public function parseResult()
  {
    $dom = new \DOMDocument('1.0', 'UTF-8');
    @$dom->loadHTML($this->content);
    $finder = new \DOMXPath($dom);
    $errors = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $this->errorClassname ')]");

    if (count($errors)) {
      $this->displayErrors($errors);
      return [];
    }

    $infos = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $this->resultClassname ')]");
    $this->result = $this->parseData($infos);
  }

  public function parseData($nodes)
  {
    [$code, $description] = explode('-', $this->innerHTML($nodes[12]));
    [$currentSituation, , , $situationDate] = explode(' ', $this->innerHTML($nodes[15]));
    
    return [
      'cnpj' => $this->innerHTML($nodes[0]),
      'ie' => $this->innerHTML($nodes[1]),
      'razao_social' => $this->innerHTML($nodes[2]),
      'logradouro' => $this->innerHTML($nodes[3]),
      'numero' => $this->innerHTML($nodes[4]),
      'complemento' => $this->innerHTML($nodes[5]),
      'bairro' => $this->innerHTML($nodes[6]),
      'cep' => $this->innerHTML($nodes[9]),
      'municipio' => $this->innerHTML($nodes[7]),
      'uf' => $this->innerHTML($nodes[8]),
      'telefone' => $this->innerHTML($nodes[10]),
      'email' => $this->innerHTML($nodes[11]),
      'data_inicio' => count($nodes) > 19 ? $this->innerHTML($nodes[14]) : $this->innerHTML($nodes[13]),
      'situacao_atual' => $currentSituation,
      'data_situacao_atual' => $situationDate,
      'data' => date('Y-m-d'),
      'hora' => date('H:i:s'),
      'atividade_principal' => [
        'codigo' => $code,
        'descricao' => $description
      ]
    ];
  }

  public function displayErrors($errors)
  {
    foreach ($errors as $error) {
      $content = ucfirst(\mb_strtolower($this->innerHTML($error)));
      echo "\033[41m$content\33[0m" . PHP_EOL;
    }
  }
}
