<?php

/**
 * @file
 * Contains \GSBConnectingLink.
 */

/**
 * Class GSBConnectingLink.
 */
class GSBConnectingLink {

  /**
   * The name of the link.
   *
   * @var string
   */
  public $name;

  /**
   * The unique alias for the link.
   *
   * @var string
   */
  public $alias;

  /**
   * The URL destination.
   *
   * @var
   */
  public $destination;

  /**
   * The link sponsor.
   *
   * @var string
   */
  public $sponsor;

  /**
   * The link type.
   *
   * @var string
   */
  public $type;

  /**
   * Returns the associative array of type options.
   *
   * @return array
   */
  public function getTypeOptions() {
    return array(
      'ejournal' => t('eJournal'),
      'database' => t('Database'),
      'ebook' => t('eBook'),
    );
  }

  /**
   * Indicates if this link is new.
   *
   * @return bool
   */
  public function isNew() {
    return empty($this->alias);
  }

  /**
   * Returns the human-readable type of link.
   *
   * @return string
   */
  public function getType() {
    $options = $this->getTypeOptions();
    return isset($options[$this->type]) ? $options[$this->type] : '';
  }

  /**
   * Deletes the connecting link.
   */
  public function delete() {
    db_delete('gsb_connecting_link')
      ->condition('alias', $this->alias)
      ->execute();
  }

  /**
   * Returns the sponsor for the link, with a fallback.
   *
   * @return string
   */
  public function getSponsor() {
    return $this->sponsor ?: variable_get('gsb_connecting_link_sponsor_default', t('Graduate School of Business Library'));
  }

  /**
   * Loads a given link by its alias.
   *
   * @param string $alias
   *
   * @return \GSBConnectingLink|bool
   *   The link object, or FALSE if none exist with that alias.
   */
  public static function loadByAlias($alias) {
    $query = db_select('gsb_connecting_link', 'cl')
      ->fields('cl')
      ->condition('alias', $alias)
      ->execute();

    return $query->fetchObject('GSBConnectingLink');
  }

  /**
   * Loads a set of links based on the current pager.
   *
   * @param array $header
   *   The header array for the paged table.
   *
   * @return \GSBConnectingLink[]
   */
  public static function loadFromPager($header) {
    $query = db_select('gsb_connecting_link', 'cl')
      ->extend('TableSort')
      ->extend('PagerDefault')
      ->fields('cl')
      ->limit(50)
      ->orderByHeader($header)
      ->execute();

    return $query->fetchAllAssoc('alias', 'GSBConnectingLink');
  }

}
