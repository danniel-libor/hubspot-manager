<p align="center">
<a href="https://packagist.org/packages/engaging-io/hubspot-manager"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/engaging-io/hubspot-manager"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/engaging-io/hubspot-manager"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About HubSpot Manager

A Laravel package to manage HubSpot API interactions with rollback capabilities.

## Usage

```php
<?php

use EngagingIo\HubSpotManager\HubSpotManager;
use HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInput;

$hubSpotManager = new HubSpotManager;

$properties1 = [
    'property_date' => '1572480000000',
    'property_radio' => 'option_1',
    'property_number' => '17',
    'property_string' => 'value',
    'property_checkbox' => 'false',
    'property_dropdown' => 'choice_b',
    'property_multiple_checkboxes' => 'chocolate;strawberry'
];

$simplePublicObjectInput = new SimplePublicObjectInput([
    'properties' => $properties1,
]);

try {
    // Call the updateDeal method on the HubSpotManager instance.
    // This method sends a request to the HubSpot API to update a deal with the given ID.
    // The second parameter, $simplePublicObjectInput, contains the new data for the deal.
    // The method returns an API response which contains the updated deal data or an error message.
    $apiResponse = $hubSpotManager->updateDeal('dealId', $simplePublicObjectInput);

    var_dump($apiResponse);
} catch (\Exception $e) {
    // Rollback any changes made during the process.
    // This method is called when an exception occurs during the process.
    // It ensures that the state of the system is consistent by undoing any changes that were made.
    $hubSpotManager->rollback();

    throw $e;
}

```

<br>

This example creates a HubSpot company and a contact object.
If an exception occurs during the process, it calls the rollback method on the HubSpotManager instance and rollback any changes made to HubSpot objects.

```php
<?php

namespace App\Http\Controllers;

use EngagingIo\HubSpotManager\HubSpotManager;
use HubSpot\Client\Crm\Companies\Model\AssociationSpec as CompanyAssociationSpec;
use HubSpot\Client\Crm\Companies\Model\PublicAssociationsForObject as CompanyPublicAssociationsForObject;
use HubSpot\Client\Crm\Companies\Model\PublicObjectId as CompanyPublicObjectId;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInputForCreate as CompanySimplePublicObjectInputForCreate;

use HubSpot\Client\Crm\Contacts\Model\AssociationSpec as ContactAssociationSpec;
use HubSpot\Client\Crm\Contacts\Model\PublicAssociationsForObject as ContactPublicAssociationsForObject;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectId as ContactPublicObjectId;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInputForCreate as ContactSimplePublicObjectInputForCreate;

class SampleController extends Controller
{
    private $hubSpotManager;

    /**
     * Construct a new instance of IndexController.
     *
     * This constructor method injects a HubSpotManager instance into the controller.
     * The HubSpotManager instance is used to interact with the HubSpot API.
     *
     * @param HubSpotManager $hubSpotManager An instance of HubSpotManager.
     */
    public function __construct(HubSpotManager $hubSpotManager)
    {
        $this->hubSpotManager = $hubSpotManager;
    }

    /**
     * Handle the incoming request.
     *
     * This method creates a HubSpot company and a contact object.
     * It first creates an association specification and a public object ID for the company,
     * then it creates a simple public object input for the company with the association and properties.
     * It then calls the createCompany method on the HubSpotManager instance with the simple public object input.
     * It repeats the same process for the contact object.
     * If an exception occurs during the process, it calls the rollback method on the HubSpotManager instance.
     * If the process is successful, it returns a JSON response with a message.
     *
     * @throws \Exception If an error occurs during the process.
     * @return \Illuminate\Http\JsonResponse A JSON response with a message.
     */
    public function __invoke()
    {
        try {
            $associationSpec1 = new CompanyAssociationSpec([
                'association_category' => 'HUBSPOT_DEFINED',
                'association_type_id' => 0
            ]);

            $to1 = new CompanyPublicObjectId([
                'id' => 'string'
            ]);

            $publicAssociationsForObject1 = new CompanyPublicAssociationsForObject([
                'types' => [$associationSpec1],
                'to' => $to1
            ]);

            $properties1 = [
                'additionalProp1' => 'string',
                'additionalProp2' => 'string',
                'additionalProp3' => 'string'
            ];

            $simplePublicObjectInputForCreate = new CompanySimplePublicObjectInputForCreate([
                'associations' => [$publicAssociationsForObject1],
                'properties' => $properties1,
            ]);

            // Create a HubSpot company object
            $this->hubSpotManager->createCompany($simplePublicObjectInputForCreate);

            $associationSpec1 = new ContactAssociationSpec([
                'association_category' => 'HUBSPOT_DEFINED',
                'association_type_id' => 0
            ]);

            $to1 = new ContactPublicObjectId([
                'id' => 'string'
            ]);

            $publicAssociationsForObject1 = new ContactPublicAssociationsForObject([
                'types' => [$associationSpec1],
                'to' => $to1
            ]);

            $properties1 = [
                'additionalProp1' => 'string',
                'additionalProp2' => 'string',
                'additionalProp3' => 'string'
            ];

            $simplePublicObjectInputForCreate = new ContactSimplePublicObjectInputForCreate([
                'associations' => [$publicAssociationsForObject1],
                'properties' => $properties1,
            ]);

            // Create a HubSpot contact object
            $this->hubSpotManager->createContact($simplePublicObjectInputForCreate);

            return response()->json('Hello World!', 200);
        } catch (\Exception $e) {
            // Rollback any changes made to HubSpot objects
            $this->hubSpotManager->rollback();

            throw $e;
        }
    }
}

```

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Danniel Libor via [danniel@engaging.io](mailto:danniel@engaging.io). All security vulnerabilities will be promptly addressed.

## License

The HubSpot Manager is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## TODO

- custom objects
- batch companies
- batch contacts
- batch deals
- batch custom objects
- tasks
- batch tasks
- notes
- batch notes
- associations
- batch associations
- better documentation
- etc.
