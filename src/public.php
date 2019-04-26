<?php

declare(strict_types=1);

use App\Service\TemplateRenderer;
use Ubnt\UcrmPluginSdk\Security\PermissionNames;
use Ubnt\UcrmPluginSdk\Service\UcrmApi;
use Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager;
use Ubnt\UcrmPluginSdk\Service\UcrmSecurity;

chdir(__DIR__);

require_once __DIR__ . '/vendor/autoload.php';

// Retrieve API connection.
$api = UcrmApi::create();

// Ensure that user is logged in.
$security = UcrmSecurity::create();
$user = $security->getUser();
if (! $user || $user->isClient) {
    \App\Http::forbidden();
}

// Retrieve renderer.
$renderer = new TemplateRenderer();

// Process submitted form.
if (
    array_key_exists('latitude', $_GET)
    && is_string($_GET['latitude'])
    && array_key_exists('longitude', $_GET)
    && is_string($_GET['longitude'])
    && array_key_exists('censusyear', $_GET)
    && is_string($_GET['censusyear'])
) {
    $parameters = [
        'latitude' => $_GET['latitude'],
        'longitude' => $_GET['longitude'],
        'censusyear' => $_GET['censusyear'],
    ];

    // force the latitude to be a decimal number
    if ($parameters['latitude']) {
        if(!preg_match('/^-?[0-9]+(\\.[0-9]+)?$/', $parameters['latitude'])) {
          $parameters['latitude'] = '';
        }
    } else {
        $parameters['latitude'] = '';
    }

    // force the longitude to be a decimal number
    if ($parameters['longitude']) {
        if(!preg_match('/^-?[0-9]+(\\.[0-9]+)?$/', $parameters['longitude'])) {
          $parameters['longitude'] = '';
        }
    } else {
        $parameters['longitude'] = '';
    }

    // force the censusyear to be in YYYY format
    if ($parameters['censusyear']) {
        if(!preg_match('/[0-9][0-9][0-9][0-9]/', $parameters['censusyear'])) {
          $parameters['censusyear'] = "2010";
        }
    } else {
        $parameters['censusyear'] = "2010";
    }

    // do the FIPS lookup
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://geo.fcc.gov/api/census/block/find?latitude=" . $parameters['latitude'] . "&longitude=" . $parameters['longitude'] . "&censusYear=" . $parameters['censusyear'] . "&format=json&showall=false");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    curl_close($ch);

    $fips = json_decode($response,true);

    $parameters['fips'] = '';
    if(isset($fips['Block'])) {
      if(isset($fips['Block']['FIPS'])) {
        $parameters['fips'] = $fips['Block']['FIPS'];
      }
    }

    $parameters['status'] = '';
    if(isset($fips['statusMessage'])) {
      $parameters['status'] = $fips['statusMessage'];
    }

    $result = [
        'latitude' => $parameters['latitude'],
        'longitude' => $parameters['longitude'],
        'censusyear' => $parameters['censusyear'],
        'fips' => $parameters['fips'],
        'status' => $parameters['status'],
    ];
}

// Render form.
$optionsManager = UcrmOptionsManager::create();

$renderer->render(
    __DIR__ . '/templates/form.php',
    [
        'ucrmPublicUrl' => $optionsManager->loadOptions()->ucrmPublicUrl,
        'result' => $result ?? [],
    ]
);


