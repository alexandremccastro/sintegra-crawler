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
   * Class that return the results.
   */
  protected $resultClassname = '';

  /**
   * Class that return the errors.
   */
  protected $errorClassname = '';

  /**
   * The content retrieved from the page.
   */
  protected $content = '';

  /**
   * The result that has been parsed.
   */
  protected $result = [];


  public function __construct(Client $httpClient, $siteURL, $captchaURL, $captchaInput, 
                              $cnpjInput, $searchTrigger, $resultClassname, $errorClassname)
  {
    $this->httpClient = $httpClient;
    $this->siteURL = $siteURL;
    $this->captchaURL = $captchaURL;
    $this->captchaInput = $captchaInput;
    $this->cnpjInput = $cnpjInput;
    $this->searchTrigger = $searchTrigger;
    $this->resultClassname = $resultClassname;
    $this->errorClassname = $errorClassname;
  }

  public function prompt()
  {
    $this->generateCookie();
    $this->getCaptcha();
    $this->searchCnpj();
    $this->getCompanyInfo();
    $this->parseResult();
  }

  public function getCaptcha()
  {
    if ($this->hasCaptcha()) {
      $this->loadCaptcha();
      echo "O captcha foi salvo em: \033[42mtmp/captcha.jpeg\033[0m" . PHP_EOL;
      $this->params[$this->captchaInput] = readline('Digite o texto do captcha: ');
    }
  }

  public function searchCnpj()
  {
    $input = readline('Digite o CNPJ da empresa: ');
    $filteredCnpj = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    $this->params[$this->cnpjInput] = $filteredCnpj;
  }

  protected function getPostInfo()
  {
    return array_merge($this->params, $this->searchTrigger);
  }

  public function getResult()
  {
    return $this->result;
  }

  public function innerHTML(\DOMElement $element)
  {
    $doc = $element->ownerDocument;

    $html = '';

    foreach ($element->childNodes as $node) {
        $html .= $doc->saveHTML($node);
    }

    return trim($html);
  }

  public abstract function generateCookie();
  public abstract function hasCaptcha();
  public abstract function loadCaptcha();
  public abstract function getCompanyInfo();
  public abstract function parseResult();
  public abstract function displayErrors($errors);
  public abstract function parseData($data);
}