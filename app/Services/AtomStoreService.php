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

    public function setProduct($payload)
    {
        $attributes = '';
        foreach ($payload['attributes'] as $key => $value) {
            $attributes .= '
                <attribute>
                    <code>
                        ' . $key . '
                    </code>
                    <name>
                        <![CDATA[ ' . $key . ' ]]>
                    </name>
                    <code_value>
                        ' . $key . '
                    </code_value>
                    <value>
                        <![CDATA[ ' . $value . ' ]]>
                    </value>
                </attribute>
            ';
        }

        $xml = '
        <products>
            <product>
                <code>' . $payload["code"] . '</code>
                <update>1</update>
                <product_name>
                    <![CDATA[ ' . $payload["name"] . ' ]]>
                </product_name>
                <price_brutto>' . $payload["price"] . '</price_brutto>
                <vat_rate>' . $payload["vat_rate"] . '</vat_rate>
                <quantity>' . $payload["stock"] . '</quantity>
                <attributes>' . $attributes . '</attributes>
            </product>
        </products>
        ';
        $message = ['xml' => $xml];
        $response = $this->client->SetProducts($this->authenticate, $message);
    }
}
