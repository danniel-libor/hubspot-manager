<?php

namespace EngagingIo\HubSpotManager\api;

use Illuminate\Support\Facades\Log;
use HubSpot\Client\Crm\Companies\ApiException;
use HubSpot\Client\Crm\Companies\Model\BatchInputSimplePublicObjectId;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectId;
use HubSpot\Client\Crm\Companies\Model\BatchInputSimplePublicObjectBatchInput;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectBatchInput;

trait CompanyApi
{
    /**
     * Create a new company in the CRM.
     *
     * This function sends a request to the CRM's API to create a new company.
     * The company's details are provided in the $simplePublicObjectInputForCreate parameter.
     * If the request is successful, it stores the company's ID and returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInputForCreate $simplePublicObjectInputForCreate The details of the company to create.
     * @return array The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function createCompany($simplePublicObjectInputForCreate)
    {
        try {
            $response = $this->request()
                ->crm()
                ->companies()
                ->basicApi()
                ->create($simplePublicObjectInputForCreate);

            $this->objects['companies']['create'][] = $response['id'];

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->create: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Rollback the creation of a company in the CRM by archiving it.
     *
     * This function sends a request to the CRM's API to archive a company.
     * The company to be archived is identified by the $companyId parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param array $ids The ID of the company to archive.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function rollbackCreatedCompanies($ids)
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
                ->companies()
                ->batchApi()
                ->archive($batchInputSimplePublicObjectId);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling batch_api->archive: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Retrieve a company's details from the CRM by its ID.
     *
     * This function sends a request to the CRM's API to retrieve the details of a company.
     * The company to be retrieved is identified by the $id parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param int $id The ID of the company to retrieve.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function getCompany($id)
    {
        try {
            $response = $this->request()
                ->crm()
                ->companies()
                ->basicApi()
                ->getById($id, false);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->get_by_id: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Update a company's details in the CRM.
     *
     * This function retrieves the current details of the company identified by the $id parameter,
     * stores these details for potential rollback, and then sends a request to the CRM's API to update the company's details.
     * The new details are provided in the $simplePublicObjectInput parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param int $id The ID of the company to update.
     * @param HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInput $simplePublicObjectInput The new details of the company.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function updateCompany($id, $simplePublicObjectInput)
    {
        try {
            $company = $this->getCompany($id);

            $this->objects['companies']['update'][$id] = $company['properties'];

            $response = $this->request()
                ->crm()
                ->companies()
                ->basicApi()
                ->update($id, $simplePublicObjectInput);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->update: ", $e->getMessage());

            throw $e;
        }
    }

    /**
     * Rollback the update of a company in the CRM by reverting its properties.
     *
     * This function sends a request to the CRM's API to update a company's properties.
     * The company to be updated is identified by the $id parameter, and the properties to be reverted are provided in the $properties parameter.
     * If the request is successful, it returns the response from the API.
     * If an ApiException occurs during the request, it logs the error message and rethrows the exception.
     *
     * @param array $object The properties to revert.
     * @return mixed The response from the CRM's API.
     * @throws ApiException If an error occurs during the API request.
     */
    public function rollbackUpdatedCompanies($object)
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
                ->companies()
                ->batchApi()
                ->update($batchInputSimplePublicObjectBatchInput);

            return $response;
        } catch (ApiException $e) {
            Log::error("Exception when calling basic_api->update: ", $e->getMessage());

            throw $e;
        }
    }
}
