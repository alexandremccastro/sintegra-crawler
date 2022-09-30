<?php

namespace Core;

use Http\Client;

abstract class Spider
{
  /**
   * Client used to make requests.
   */
  protected $httpClient;

  /**
   * The spider start point.
   */
  protected $siteURL = null;

  /**
   * The URL used to load captcha image.
   */
  protected $captchaURL = null;

  /**
   * The name of the input used to type captcha value.
   */
  protected $captchaInput = '';

   /**
   * The name of the input used to type cnpj value.
   */
  protected $cnpjInput = '';

  /**
   * Value of the input that triggers the search.
   */
  protected $searchTrigger = [];

  /**
   * The result retrieved from the page.
   */
  protected $results = [];


  public function __construct(Client $httpClient, $siteURL, $captchaURL, $captchaInput, $cnpjInput, $searchTrigger)
  {
    $this->httpClient = $httpClient;
    $this->siteURL = $siteURL;
    $this->captchaURL = $captchaURL;
    $this->captchaInput = $captchaInput;
    $this->cnpjInput = $cnpjInput;
    $this->searchTrigger = $searchTrigger;
  }

  public function prompt()
  {
    $this->plugIn();

    $params = [];

    if ($this->hasCaptcha()) {
      $this->loadCaptcha();
      echo "\033[01;31mO captcha foi salvo em: tmp/captcha.jpeg\033[0m\n";
      $params[$this->captchaInput] = readline('Digite o texto do captcha: ');
    }

    $params[$this->cnpjInput] = readline('Digite o CNPJ da empresa: ');
    $params = array_merge($params, $this->searchTrigger);
    $content = $this->postInfo($params);
    $this->result = $this->parseResult($content, 'form_conteudo');
  }

  public function getResult()
  {
    return $this->result;
  }

  public abstract function plugIn();
  public abstract function hasCaptcha();
  public abstract function loadCaptcha();
  public abstract function postInfo($params = []);
  public abstract function parseResult($content, $classname);
  public abstract function parseData($data);
}