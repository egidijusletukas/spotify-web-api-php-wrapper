<?php

namespace SpotifyClient;

use GuzzleHttp\Client;
use SpotifyClient\Constant\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Authorization.
 */
class Authorization extends Client
{
    const URI = 'https://accounts.spotify.com/authorize';

    /**
     * @var array
     */
    private $options;

    /**
     * Authorization constructor.
     *
     * @param OptionsResolver $optionsApp
     */
    public function __construct(OptionsResolver $optionsApp)
    {
        $this->options = $optionsApp->resolve();

        parent::__construct(['base_uri' => self::URI]);
    }

    /**
     * @return OptionsResolver
     */
    public static function getOptionsApp() : OptionsResolver
    {
        $required = [
            'client_id',
            'client_secret',
            'redirect_uri',
        ];
        $defaults = [
            'state' => null,
            'response_type' => 'code',
        ];

        return (new OptionsResolver())
            ->setRequired($required)
            ->setDefaults($defaults);
    }

    public function getHeaders() : array
    {
        $this->authorizeApplication();

        dump($response);
        die;
    }

    private function authorizeApplication()
    {
        $options = [
            'query' => [
                'client_id' => $this->options['client_id'],
                'response_type' => $this->options['response_type'],
                'redirect_uri' => $this->options['redirect_uri'],
                'state' => $this->options['state'],
            ],
        ];
        $response = $this->request(Request::GET, '', $options);

        dump($response->getBody()->getContents());
        die;
    }
}
