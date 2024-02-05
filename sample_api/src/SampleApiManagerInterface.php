<?php

namespace Drupal\sample_api;

/**
 * Provides a sample api service.
 */
interface SampleApiManagerInterface {

  /**
   * 
   */
  public function getClients();

  public function insertData(object $response);

  public function checkExisted(object $data);

  public function getToken();

  public function getData(array $filter);

  public function getServices(string $token);

  public function getSubServices(array $sid);

  public function insertNewData(string $client_id, string $client_number, string $service_name_text, string $sub_service_name_text);
}
