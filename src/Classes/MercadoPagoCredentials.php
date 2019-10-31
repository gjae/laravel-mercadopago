<?php
namespace Gjae\MercadoPago\Classes;

use Gjae\MercadoPago\Exceptions\NoConfigException;
class MercadoPagoCredentials
{

    private $token = "";

    private $public_key = "";

    public function __construct(array $credentials)
    {
        if( empty( $credentials['access_token'] ) || empty( $credentials['public_key'] ) )
            return new NoConfigException('Access token or public key no settings');

        $this->setAccessToken( $credentials['access_token'] );
        $this->setPublicKey( $credentials['public_key'] );

    }

    /**
     * Undocumented function
     *
     * @param string $access_token
     * @return void
     */
    public function setAccessToken($access_token = "")
    {
        if( empty( $access_token ) ) throw new NoConfigException('Access token not config');
        $this->token = $access_token;
    }

    /**
     * Undocumented function
     *
     * @param string $public_key
     * @return void
     */
    public function setPublicKey($public_key = "")
    {
        if( empty($public_key) ) throw new NoConfigException('Public key not config');
        $this->public_key = $public_key;
    }


    /**
     * Undocumented function
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->public_key;
    }

    /**
     * Undocumented function
     *
     * @param array $credentials
     * @return void
     */
    public function set(array $credentials)
    {
        $this->setAccessToken( $credentials['access_token'] );
        $this->setPublicKey( $credentials['public_key'] );
    }
}