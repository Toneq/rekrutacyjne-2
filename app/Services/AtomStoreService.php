<?php

namespace App\Services;

use SoapClient;
use SoapFault;

class AtomStoreService
{
    protected $wsdl;
    protected $username;
    protected $password;
    protected $client;
    protected $authenticate;

    public function __construct()
    {
        $this->wsdl = env('ATOMSTORE_WSDL');
        $this->username = env('ATOMSTORE_API_USER');
        $this->password = env('ATOMSTORE_API_PASSWORD');

        $this->client = new \SoapClient($this->wsdl);
        $this->authenticate = ['login' => $this->username, 'password' => $this->password];
    }
}
