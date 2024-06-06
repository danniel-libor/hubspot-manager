<?php

namespace EngagingIo\HubSpotManager\api;

use Illuminate\Support\Facades\Log;
use HubSpot\Client\Crm\Contacts\ApiException;
use HubSpot\Client\Crm\Contacts\Model\BatchInputSimplePublicObjectId;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectId;
use HubSpot\Client\Crm\Contacts\Model\BatchInputSimplePublicObjectBatchInput;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectBatchInput;

trait ContactApi
{
    /**
     * Create a new contact in the CRM.
     *
     * This function sends a request to the CRM's API to create a new contact.
     * The contact's details are provided in the $simplePublicObjectInputForCreate parameter.
     * If the request is successful, it stores the contact's ID and returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param HubSpot\Client\Crm\Contacts\Model\simplePublicObjectInputForCreate $simplePublicObjectInputForCreate The details of the contact to create.
     * @return array The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function createContact($simplePublicObjectInputForCreate)
    {
        try {
            $response = $this->request()
                ->crm()
                ->contacts()
                ->basicApi()
                ->create($simplePublicObjectInputForCreate);

            $this->objects['contacts']['create'][] = $response['id'];

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->create: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Rollback the creation of a contact in the CRM by archiving it.
     *
     * This function sends a batch request to the CRM's API to archive multiple contacts.
     * The contacts to be archived are identified by the $ids parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param array $ids An array of IDs of the contacts to archive.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function rollbackCreatedContacts($ids)
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
                ->contacts()
                ->batchApi()
                ->archive($batchInputSimplePublicObjectId);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling batch_api->archive: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Retrieve a contact's details from the CRM by its ID.
     *
     * This function sends a request to the CRM's API to retrieve the details of a contact.
     * The contact to be retrieved is identified by the $id parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param int $id The ID of the contact to retrieve.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function getContact($id)
    {
        try {
            $response = $this->request()
                ->crm()
                ->contacts()
                ->basicApi()
                ->getById($id, false);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->get_by_id: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Update a contact's details in the CRM.
     *
     * This function retrieves the current details of the contact identified by the $id parameter,
     * stores these details for potential rollback, and then sends a request to the CRM's API to update the contact's details.
     * The new details are provided in the $simplePublicObjectInput parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param int $id The ID of the contact to update.
     * @param HubSpot\Client\Crm\Contacts\Model\simplePublicObjectInput $simplePublicObjectInput The new details of the contact.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function updateContact($id, $simplePublicObjectInput)
    {
        try {
            $company = $this->getCompany($id);

            $this->objects['contacts']['update'][$id] = $company['properties'];

            $response = $this->request()
                ->crm()
                ->contacts()
                ->basicApi()
                ->update($id, $simplePublicObjectInput);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->update: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Rollback the update of a contact in the CRM by reverting its properties.
     *
     * This function sends a batch request to the CRM's API to update multiple contacts' properties.
     * The contacts to be updated and their properties to be reverted are provided in the $object parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param array $object An associative array where the key is the contact ID and the value is an array of properties to revert.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function rollbackUpdatedContacts($object)
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
                ->contacts()
                ->batchApi()
                ->update($batchInputSimplePublicObjectBatchInput);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->update: ", $e->getMessage());

            throw $e;
        }
    }
}
