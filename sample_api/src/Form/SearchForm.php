<?php

namespace Drupal\sample_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\Request;
class SearchForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructs a GDRateSubmissionForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Request $request) {
    $this->entityTypeManager = $entity_type_manager;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('request_stack')->getCurrentRequest(),
    );
  }

  public function getFormId() {
    return 'sample_search_form';
}

  /**
   * {@inheritdoc}
   */
  public function buildform(array $form, FormStateInterface $form_state) {
    $form['search_form'] = [
      '#type' => 'container',
      '#attributes' =>[
        'class' => ['rate-submission-form'],
      ],
      '#prefix' => '<div id="submission-form">',
      '#suffix' => '</div>',
    ];

    $form['search_form']['id'] = [
      '#type' => 'textfield',
      '#title' => t('ID'),
      '#default_value' => $this->request->get('id'),
    ];

    $form['search_form']['client_id'] = [
      '#type' => 'textfield',
      '#title' => t('Client id'),
      '#default_value' => $this->request->get('client_id'),
    ];

    $form['search_form']['client_number'] = [
      '#type' => 'textfield',
      '#title' => t('Client number'),
      '#default_value' => $this->request->get('client_number'),
    ];

    $form['search_form']['service_name'] = [
      '#type' => 'textfield',
      '#title' => t('Service Name'),
      '#default_value' => $this->request->get('service_name'),
    ];

    $form['search_form']['sub_service_name'] = [
      '#type' => 'textfield',
      '#title' => t('Sub Service Name'),
      '#default_value' => $this->request->get('sub_service_name'),
    ];

    $form['submission_form']['actions'] = [
      '#type' => 'actions',
    ];

    $form['submission_form']['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => 'Search',
        '#button_type' => 'primary',
        '#submit' => ['::submitForm'],
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $filter = '?';
    $add_filter = FALSE;
    $target_url = '/sample-api/page-1';

    if ($id = $form_state->getValue('id')) {
      $filter .= $filter != '?' ? '&id=' . $id : 'id=' . $id;
      $add_filter = TRUE;
    }

    if ($client_id = $form_state->getValue('client_id')) {
      $filter .= $filter != '?' ? '&client_id=' . $client_id : 'client_id=' . $client_id;
      $add_filter = TRUE;
    }

    if ($client_number = $form_state->getValue('client_number')) {
      $filter .= $filter != '?' ? '&client_number=' . $client_number : 'client_number=' . $client_number;
      $add_filter = TRUE;
    }

    if ($service_name = $form_state->getValue('service_name')) {
      $filter .= $filter != '?' ? '&service_name=' . $service_name : 'service_name=' . $service_name;
      $add_filter = TRUE;
    }

    if ($sub_service_name = $form_state->getValue('sub_service_name')) {
      $filter .= $filter != '?' ? '&sub_service_name=' . $sub_service_name : 'sub_service_name=' . $sub_service_name;
      $add_filter = TRUE;
    }
    
    if ($add_filter) {
      $target_url .= $filter;
    }
    
    /** Search */
    $target_url = Url::fromUserInput($target_url);
    $form_state->setRedirectUrl($target_url);
  }
}
