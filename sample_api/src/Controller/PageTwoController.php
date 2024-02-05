<?php
namespace Drupal\sample_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\sample_api\SampleApiManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PageTwoController extends ControllerBase{
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
   * Constructs a new GDReact Controller.
   * 
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * 
   * @param \Drupal\sample_api\SampleApiManagerInterface $api_manager
   *   The sample api Manager.
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

  public function pageTwo() {
    $build = [];
    $form = \Drupal::formBuilder()->getForm('Drupal\sample_api\Form\ApiDataForm');
    
    $build['services'] = $form;

    return $build;
  }
}