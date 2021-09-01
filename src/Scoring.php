<?php

namespace Arttiger\Scoring;

use Arttiger\Scoring\Factories\ProviderFactory;
use Arttiger\Scoring\Interfaces\Provider;
use Illuminate\Support\Facades\Config;

class Scoring
{
    protected $provider;
    protected $config;

    /**
     * Scoring constructor.
     *
     * @param Provider|null $provider
     */
    public function __construct(Provider $provider = null)
    {
        $this->config = (class_exists('Config') ? Config::get('scoring') : []);
        $this->provider = $provider;
    }

    /**
     * @param       $service
     * @param array $params
     *
     * @return mixed
     */
    public function scoring($service, $params = [])
    {
        $factory = new ProviderFactory();
        if ($this->provider = $factory->getProvider($service)) {
            return $this->provider->getScoring($params);
        }

        return false;
    }

    /**
     * @param       $service
     * @param array $params
     *
     * @return bool
     */
    public function status($service, $params = [])
    {
        $factory = new ProviderFactory();
        if ($this->provider = $factory->getProvider($service)) {
            return $this->provider->sendFeedback($params);
        }

        return false;
    }

    /**
     * @param       $service
     * @param array $params
     *
     * @return bool
     */
    public function ubkiReport($service, $params = [])
    {
        $factory = new ProviderFactory();
        if ($this->provider = $factory->getProvider($service)) {
            return $this->provider->getUbki($params);
        }

        return false;
    }

    /**
     * @param       $service
     * @param array $params
     *
     * @return mixed
     */
    public function pre_scoring($service, $params = [])
    {
        $factory = new ProviderFactory();
        if ($this->provider = $factory->getProvider($service)) {
            return $this->provider->getPreScoring($params);
        }

        return false;
    }
}
