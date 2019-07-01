# EUROTEXT AG - API Client for PHP

If you want to connect to the live API server, use `https://api.eurotext.de`, for sandbox testing use `https://sandbox.api.eurotext.de`.

**Note:** By default the client connects to the sandbox API! This behaviour can be configured within the configuration object.


# Available Endpoints

The API provides various endpoints. This Client only implements access to the following endpoints:


## Manage Projects

### Create (POST)

connects to `/api/v1/project/{project}` 

Creates a new project, expects a `ProjectPostRequest` object

### Retrieve (GET)

connects to `/api/v1/project/{project}` 

Retrieves project data, expects an `ProjectGetRequest` object


### Change Status (PATCH)

connects to `/api/v1/transition/project/{project}.json`

Transitions project status, expects a `ProjectTransitionRequest` object


### Pseudo-Translate (PATCH)

connects to `/api/v1/project/translate/{project}.json`

Translates all items in a project with pseudo-translated strings, expects a `ProjectTranslateRequest` object


## Manage Items


### Create (POST)

connects to `/api/v1/project/{project}/item/{item}`

creates a new item, expects a `ItemPostRequest` object


### Retrieve (GET)

connects to `/api/v1/project/{project}/item/{item}`

Retrieves item data, expects a `ItemGetRequest` object


# Inner Workings

This client uses object oriented programming and dependency injection.

To create a project create request you first have to instantiate the client with a configuration:

```php
// define your api key provided by Eurotext
$apiKey = 'myCustomAPIKey';

// create configuration with your key
$config = new Configuration();
$config->setApiKey($apiKey);

// instantiate project API object
$projectApi = new ProjectV1Api($config);

// create project data object
$projectData = new ProjectData('Project Description');

// create post request object with project data
$request = new ProjectPostRequest('Project Name', $projectData, ProjectTypeEnum::QUOTE());

// send request via project API's POST request
$response = $projectApi->post($request);

// retrieve API project id from result
$createdProjectId = $response->getId();

```

Using dependency injection you can create your own data or request objects as long as you implement the required interfaces. These are required by the corresponding methods and will throw exceptions if you don't implement them. This approach forces you to create all required methods needed by the API.
