<?php

namespace EngagingIo\HubSpotManager;

use HubSpot\Factory;
use EngagingIo\HubSpotManager\api\CompanyApi;
use EngagingIo\HubSpotManager\api\ContactApi;
use EngagingIo\HubSpotManager\api\DealApi;

class HubSpotManager
{
    use CompanyApi,
        ContactApi,
        DealApi;

    protected $apiKey;
    protected $objects = [];

    /**
     * Create a new instance of the HubSpotManager class.
     *
     * This constructor is responsible for setting up any necessary properties
     * or dependencies for the HubSpotManager class. Currently, it does not
     * require any parameters or perform any actions.
     */
    public function __construct()
    {
        $this->apiKey = config('hubspotmanager.api_key');
    }

    /**
     * Create a new instance of the Factory class with an access token.
     *
     * This function attempts to create a new instance of the Factory class using the provided API key.
     * If the creation is successful, it returns the new instance.
     * If an exception occurs during the creation, it rethrows the exception.
     *
     * @return Factory The new instance of the Factory class.
     * @throws \Exception If an error occurs during the creation of the Factory instance.
     */
    protected function request()
    {
        try {
            return Factory::createWithAccessToken($this->apiKey);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Rollback any changes made to HubSpot objects.
     *
     */
    public function rollback()
    {
        foreach ($this->objects as $type => $objects) {
            foreach ($objects as $api => $object) {
                switch ($type) {
                    case 'companies': {
                            switch ($api) {
                                case 'create': {
                                        $this->rollbackCreatedCompanies($object);
                                        break;
                                    }

                                case 'update': {
                                        $this->rollbackUpdatedCompanies($object);
                                        break;
                                    }
                            }

                            break;
                        }

                    case 'contacts': {
                            switch ($api) {
                                case 'create': {
                                        $this->rollbackCreatedContacts($object);
                                        break;
                                    }

                                case 'update': {
                                        $this->rollbackUpdatedContacts($object);
                                        break;
                                    }
                            }

                            break;
                        }

                    case 'deals': {
                            switch ($api) {
                                case 'create': {
                                        $this->rollbackCreatedDeals($object);
                                        break;
                                    }

                                case 'update': {
                                        $this->rollbackUpdatedDeals($object);
                                        break;
                                    }
                            }

                            break;
                        }
                }
            }
        }
    }
}
