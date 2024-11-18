<?php

namespace Drupal\ucb_article_syndication;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\Entity\Node;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\pathauto\PathautoState;
use Psr\Log\LoggerInterface;

/**
 * The Article Syndication service contains functions used by the module.
 */
class ArticleSyndication {

  /**
   * The path alias at which the syndication article list should reside.
   */
  const SYNDICATION_PATH = '/syndicate';

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The logger channel for this module.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs the Article Syndication service.
   *
   * @param \Drupal\Core\Extension\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger channel for this module.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    AliasManagerInterface $alias_manager,
    LoggerInterface $logger
  ) {
    $this->configFactory = $config_factory;
    $this->aliasManager = $alias_manager;
    $this->logger = $logger;
  }

  /**
   * Shows the syndication fields in the article sidebar.
   *
   * This function modifies the form display of the `ucb_article` content type
   * to display fields for entering taxonomy terms for audience and unit.
   */
  public function showSyndicationFields() {
    $articleContentTypeConfig = $this->configFactory->getEditable('core.entity_form_display.node.ucb_article.default');

    $content = $articleContentTypeConfig->get('content');
    $thirdPartySettings = $articleContentTypeConfig->get('third_party_settings');
    $hidden = $articleContentTypeConfig->get('hidden');

    $content['field_syndication_audience'] = [
      'type' => 'entity_reference_autocomplete',
      'weight' => 98,
      'region' => 'content',
      'settings' => [
        'match_operator' => 'CONTAINS',
        'match_limit' => 10,
        'size' => 60,
        'placeholder' => '',
      ],
      'third_party_settings' => [],
    ];
    $content['field_syndication_unit'] = [
      'type' => 'entity_reference_autocomplete',
      'weight' => 99,
      'region' => 'content',
      'settings' => [
        'match_operator' => 'CONTAINS',
        'match_limit' => 10,
        'size' => 60,
        'placeholder' => '',
      ],
      'third_party_settings' => [],
    ];

    $thirdPartySettings['field_group']['group_syndication'] = [
      'children' => [
        'field_syndication_audience',
        'field_syndication_unit',
      ],
      'label' => 'Syndication',
      'region' => 'content',
      'parent_name' => '',
      'weight' => 12,
      'format_type' => 'details_sidebar',
      'format_settings' => [
        'classes' => '',
        'show_empty_fields' => FALSE,
        'id' => '',
        'label_as_html' => FALSE,
        'open' => FALSE,
        'description' => '',
        'required_fields' => TRUE,
        'weight' => 0,
        'formatter' => 'closed',
        'direction' => 'vertical',
        'width_breakpoint' => 640,
      ],
    ];

    unset($hidden['field_syndication_audience']);
    unset($hidden['field_syndication_unit']);

    $articleContentTypeConfig
      ->set('content', $content)
      ->set('third_party_settings', $thirdPartySettings)
      ->set('hidden', $hidden)
      ->save();
  }

  /**
   * Creates the `/syndication` article list.
   */
  public function createSyndicationArticleList() {
    if (preg_match('/node\/(\d+)/', $this->aliasManager->getPathByAlias($this::SYNDICATION_PATH))) {
      $this->logger->warning('A syndication article list wasn’t created because a node already exists at that path.');
    }
    else {
      $node = Node::create([
        'type' => 'ucb_article_list',
        'title' => 'Article Results',
        'path' => ['alias' => $this::SYNDICATION_PATH, 'pathauto' => PathautoState::SKIP],
        'body' => '',
      ]);
      $node->enforceIsNew()->save();
    }
  }

}
