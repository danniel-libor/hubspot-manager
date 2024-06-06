<?php

namespace EngagingIo\HubSpotManager\api;

use Illuminate\Support\Facades\Log;
use HubSpot\Client\Crm\Deals\ApiException;
use HubSpot\Client\Crm\Deals\Model\BatchInputSimplePublicObjectId;
use HubSpot\Client\Crm\Deals\Model\SimplePublicObjectId;
use HubSpot\Client\Crm\Deals\Model\BatchInputSimplePublicObjectBatchInput;
use HubSpot\Client\Crm\Deals\Model\SimplePublicObjectBatchInput;

trait DealApi
{
    /**
     * Create a new deal in the CRM.
     *
     * This function sends a request to the CRM's API to create a new deal.
     * The deal's details are provided in the $simplePublicObjectInputForCreate parameter.
     * If the request is successful, it stores the deal's ID and returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param HubSpot\Client\Crm\Deals\Model\simplePublicObjectInputForCreate $simplePublicObjectInputForCreate The details of the deal to create.
     * @return array The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function createDeal($simplePublicObjectInputForCreate)
    {
        try {
            $response = $this->request()
                ->crm()
                ->deals()
                ->basicApi()
                ->create($simplePublicObjectInputForCreate);

            $this->objects['deals']['create'][] = $response['id'];

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->create: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Rollback the creation of multiple deals in the CRM by archiving them.
     *
     * This function sends a batch request to the CRM's API to archive multiple deals.
     * The deals to be archived are identified by the $ids parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param array $ids An array of IDs of the deals to archive.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function rollbackCreatedDeals($ids)
    {
        try {
            $simplePublicObjectId = [];

            foreach ($ids as $id) {
                $simplePublicObjectId[] = new SimplePublicObjectId([
                    'id' => $id,
                ]);
            }

            $batchInputSimplePublicObjectId = new BatchInputSimplePublicObjectId([
                'inputs' => $simplePublicObjectId,
            ]);

            $response = $this->request()
                ->crm()
                ->deals()
                ->batchApi()
                ->archive($batchInputSimplePublicObjectId);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling batch_api->archive: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Retrieve a deal's details from the CRM by its ID.
     *
     * This function sends a request to the CRM's API to retrieve the details of a deal.
     * The deal to be retrieved is identified by the $id parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param int $id The ID of the deal to retrieve.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function getDeal($id)
    {
        try {
            $response = $this->request()
                ->crm()
                ->deals()
                ->basicApi()
                ->getById($id, false);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->get_by_id: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Update a deal's details in the CRM.
     *
     * This function retrieves the current details of the deal identified by the $id parameter,
     * stores these details for potential rollback, and then sends a request to the CRM's API to update the deal's details.
     * The new details are provided in the $simplePublicObjectInput parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param int $id The ID of the deal to update.
     * @param HubSpot\Client\Crm\Deals\Model\simplePublicObjectInput $simplePublicObjectInput The new details of the deal.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function updateDeal($id, $simplePublicObjectInput)
    {
        try {
            $company = $this->getCompany($id);

            $this->objects['deals']['update'][$id] = $company['properties'];

            $response = $this->request()
                ->crm()
                ->deals()
                ->basicApi()
                ->update($id, $simplePublicObjectInput);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->update: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Rollback the update of multiple deals in the CRM by reverting their properties.
     *
     * This function sends a batch request to the CRM's API to update multiple deals' properties.
     * The deals to be updated and their properties to be reverted are provided in the $object parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param array $object An associative array where the key is the deal ID and the value is an array of properties to revert.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function rollbackUpdatedDeals($object)
    {
        try {
            $simplePublicObjectBatchInput = [];

            foreach ($object as $id => $properties) {
                $simplePublicObjectBatchInput[] = new SimplePublicObjectBatchInput([
                    'id' => $id,
                    'properties' => $properties,
                ]);
            }

            $batchInputSimplePublicObjectBatchInput = new BatchInputSimplePublicObjectBatchInput([
                'inputs' => $simplePublicObjectBatchInput,
            ]);

            $response = $this->request()
                ->crm()
                ->deals()
                ->batchApi()
                ->update($batchInputSimplePublicObjectBatchInput);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->update: ", $e->getMessage());

            throw $e;
        }
    }
}
