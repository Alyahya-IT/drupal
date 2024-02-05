<?php

namespace Drupal\sample_api;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\sample_api\SampleApiManagerInterface;
use GuzzleHttp\ClientInterface;

/**
 * Class Create
 * @package Drupal\sample_api
 */
class SampleApiManager implements SampleApiManagerInterface{

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The Current User.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  
  /**
   * The Http Client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Constructs a new Sample API Manager service.
   * 
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manage.
   * 
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * 
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * 
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The http client.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection, AccountProxyInterface $current_user, ClientInterface $http_client) {
    $this->entityTypeManager = $entity_type_manager;
    $this->connection = $connection;
    $this->currentUser = $current_user;
    $this->client = $http_client;
  }

  public function getClients() {
    $request = $this->client->get('https://sample-api.dev.weebpal.com/api/clients.php', [
      'auth' => ['weebpal', 'weebpal']
    ]);
    $response = $request->getBody();
    $response = json_decode($request->getBody());
    $this->insertData($response->clients);

    return $response;
  }

  public function getData(array $filter) {
    $fields = ['id', 'client_id', 'client_number', 'service_name', 'sub_service_name'];
    $query = $this->connection->select('sample_api_data', 's');
    $query->fields('s', $fields);

    foreach ($filter as $field => $value) {
      if (in_array($field, $fields)) {
        $query->condition('s.' . $field, $value);
      }
    }
    $result = $query->execute();

    return $result->fetchAll();
  }

  public function insertData(object $response) {
    foreach ($response as $index => $value) {
      $check = $this->checkExisted($value);

      if ($check === FALSE) {     
        $result = $this->connection->insert('sample_api_data')
        ->fields([
          // 'id' => $response->id->$index,
          'client_id' => $value->client_id,
          'client_number' => $value->client_number,
          'service_name' => $value->service_name,
          'sub_service_name' => implode(",", $value->sub_service_name),
        ])
        ->execute();
      }
    }
  }

  public function checkExisted(object $data) {
    $query  = $this->connection->select('sample_api_data', 's');
    $query->addField('s', 'id');
    $query->condition('s.client_id', $data->client_id);
    $query->condition('s.client_number', $data->client_number);
    $query->condition('s.service_name', $data->service_name);
    $query->condition('s.sub_service_name', implode(",", $data->sub_service_name));
    $result = $query->execute();

    return $result->fetchField();
  }

  public function getToken() {
    $request = $this->client->post('https://sample-api.dev.weebpal.com/api/token.php', [
      'auth' => ['weebpal', 'weebpal'],
    ]);

    $response = $request->getBody();
    $response = json_decode($request->getBody());

    return $response;
  }

  public function getServices(string $token) {
    $request = $this->client->get('https://sample-api.dev.weebpal.com/api/services.php?token=' . $token, [
      'auth' => ['weebpal','weebpal'],
      // 'headers' => ['token' => $token],
    ]);
    
    $response = $request->getBody();
    $response = json_decode($request->getBody());

    return (array) $response;
  }

  public function getSubServices(array $sid) {
    $result = [];

    foreach ($sid as $value) {
      $request = $this->client->get('https://sample-api.dev.weebpal.com/api/sub_services.php?sid=' . $value, [
        'auth' => ['weebpal','weebpal'],
      ]);
      $response = $request->getBody();
      $response = (array) json_decode($request->getBody());
      $result = array_merge($result, $response);
    }
    
    return $result;
  }

  public function insertNewData(string $client_id, string $client_number, string $service_name_text, string $sub_service_name_text) {
    // Check data existed
    $query  = $this->connection->select('sample_api_data', 's');
    $query->addField('s', 'id');
    $query->condition('s.client_id', $client_id);
    $query->condition('s.client_number', $client_number);
    $query->condition('s.service_name', $service_name_text);
    $query->condition('s.sub_service_name', $sub_service_name_text);
    $result = $query->execute()->fetchField();

    if ($result == NULL) {
      $result = $this->connection->insert('sample_api_data')
      ->fields([
        'client_id' => $client_id,
        'client_number' => $client_number,
        'service_name' => $service_name_text,
        'sub_service_name' => $sub_service_name_text,
      ])
      ->execute();
    }
  }
}
