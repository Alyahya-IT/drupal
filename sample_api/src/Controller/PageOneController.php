<?php
namespace Drupal\sample_api\Controller;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\sample_api\SampleApiManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PageOneController extends ControllerBase{
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
   * The Current User.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The Cache Tags Invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**  
   * Constructs a new API Controller.
   * 
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * 
   * @param \Drupal\sample_api\SampleApiManagerInterface $api_manager
   *   The GD React Manager.
   * 
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Request $request, SampleApiManagerInterface $api_manager, AccountProxyInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->request = $request;
    $this->apiManager = $api_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('sample_api.manager'),
      $container->get('current_user'),
    );
  }

  public function pageOne() {
    $rows = [];
    $data = $this->apiManager->getClients();
    $filter = $this->request->query->all();
    $data = $this->apiManager->getData($filter);
    $form = \Drupal::formBuilder()->getForm('Drupal\sample_api\Form\SearchForm');
    $header = [
      'col1' => t('Id'),
      'col2' => t('Client Id'),
      'col3' => t('Client Number'),
      'col4' => t('Service Name'),
      'col5' => t('Sub Service Name'),
    ];

    foreach ($data as $row) {
      $rows[] = (array) $row;
    }

    $build['add_btn'] = ['#markup' => '<a href="/sample-api/page-2">Add</a>'];
    $build['form'] = $form;

    if ($rows) {
      $build['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
    }
    else {
      $build['table'] = ['#markup' => '<div class="result-search"><span>No result</span></div>'];
    }
    

    return $build;
  }
}