<?php

namespace Drupal\news_content_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Generates api of node using tags.
 */
class Api extends ControllerBase {

  /**
   * Provides an interface for entity type managers.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Storeing entityTypeManagerInterface in $et.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $et
   *   Provides an interface for entity type managers.
   */
  public function __construct(EntityTypeManagerInterface $et) {
    $this->entityManager = $et;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Creating api for a node using tags.
   *
   * @param \Symfony\Component\HttpFoundation\Request $req
   *   Request represents an HTTP request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Returns data in JSON format.
   */
  public function view(Request $req) {
    $tags = $req->query->get('tags');
    $header_key = $req->query->get('header');
    $header = $this->config('form.settings')->get('api_key');
    if ($header == $header_key) {
      $terms = $this->entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadByProperties([
          'vid' => 'tags',
          'name' => $tags,
        ]);
      if (!empty($terms)) {
        $term = reset($terms);
        $tid = (int) $term->id();
      }
      $node_data = $this->entityTypeManager()->getStorage('node');
      $nodes = $node_data->loadByProperties([
        'type' => 'news',
        'field_tag' => $tid,
      ]);
      $data['nodes'] = [];
      foreach ($nodes as $node) {
        $values['Title'] = $node->get('title')->value;
        $values['Body'] = $node->get('body')->value;
        $values['Node_Count'] = $node->get('field_node_view_count')->value;
        $values['Publish'] = $node->get('field_news_published_date')->value;
        $data['nodes'][] = $values;
      }
      return new JsonResponse($data);
    }
  }

}
