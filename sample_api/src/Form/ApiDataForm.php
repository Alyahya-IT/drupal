<?php

namespace Drupal\sample_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\sample_api\SampleApiManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiDataForm extends FormBase {

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
   * The request.
   *
   * @var \Drupal\sample_api\SampleApiManagerInterface
   */
  protected $apiManager;

  /**
   * Constructs a GDRateSubmissionForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * 
   * @param \Drupal\sample_api\SampleApiManagerInterface $api_manager 
   *   The sample api Manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Request $request, SampleApiManagerInterface $api_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->request = $request;
    $this->apiManager = $api_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('sample_api.manager'),
    );
  }

  public function getFormId() {
    return 'sample_apidata_form';
}

  /**
   * {@inheritdoc}
   */
  public function buildform(array $form, FormStateInterface $form_state) {
    $token = $this->apiManager->getToken();
    $services = $this->apiManager->getServices($token);
    $selected = [$form_state->getValue('services')];
    $sub_services = [];

    if ($selected !== NULL) {
      $sub_services = $this->apiManager->getSubServices($selected);
    }

    $form['service_form'] = [
      '#type' => 'container',
      '#attributes' =>[
        'class' => ['service-form'],
      ],
    ];

    $form['service_form']['services'] = [
      // '#multiple' => TRUE,
      '#empty_option' => '- Select the service -',
      '#type' => 'select',
      '#title' => $this->t('Services'),
      '#options' => $services,
      '#default_value' => '',
      '#ajax' => [
        'callback' => '::myAjaxCallback',
        'event' => 'change',
        'wrapper' => 'sub-services',
      ],
      '#required' => TRUE,
    ];
    

    $form['service_form']['sub_services'] = [
      '#multiple' => TRUE,
      '#empty_option' => '- Select the sub-service -',
      '#type' => 'select',
      '#title' => $this->t('Sub Services'),
      '#options' => $sub_services,
      '#default_value' => '',
      '#prefix' => '<div id="sub-services">',
      '#suffix' => '</div>',
      '#required' => TRUE,
    ];

    $form['service_form']['client_id'] = [
      '#type' => 'textfield',
      '#title' => t('Client Id'),
      '#required' => TRUE,
    ];

    $form['service_form']['client_number'] = [
      '#type' => 'textfield',
      '#title' => t('Client Number'),
      '#required' => TRUE,
    ];

    $form['service_form']['actions'] = [
      '#type' => 'actions',
    ];

    $form['service_form']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
      '#button_type' => 'primary',
      '#submit' => ['::submitForm'],
    ];

    

    $form['#attached']['library'][] = 'sample_api/multiple-select';

    return $form;
  }

  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
    return $form['service_form']['sub_services'];
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $service_name = $form_state->getValue('services');
    $service_name_text = $form['service_form']['services']['#options'][$service_name];
    $sub_service_name = $form_state->getValue('sub_services');
    $client_id = $form_state->getValue('client_id');
    $client_number = $form_state->getValue('client_number');
    $sub_service_name_text = [];

    foreach ($sub_service_name as $value) {
      $sub_service_name_text[] = $form['service_form']['sub_services']['#options'][$value];
    }
    $sub_service_name_text = implode(",", $sub_service_name_text);

    // Call api to insert data
    $this->apiManager->insertNewData($client_id, $client_number, $service_name_text, $sub_service_name_text);
    $this->messenger()->addStatus($this->t('Insert data'));

    /** Search */
    $target_url = '/sample-api/page-1';
    $target_url = Url::fromUserInput($target_url);
    $form_state->setRedirectUrl($target_url);
  }
}
