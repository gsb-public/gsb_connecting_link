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
   * @var string
   */
  public $name;

  /**
   * @var string
   */
  public $alias;

  /**
   * @var
   */
  public $destination;

  /**
   * @var string
   */
  public $sponsor;

  /**
   * @var string
   */
  public $type;

  /**
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
   * @return bool
   */
  public function isNew() {
    return empty($this->alias);
  }

  /**
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
   * @return string
   */
  public function getSponsor() {
    $default = variable_get('gsb_connecting_link_sponsor_default', t('Graduate School of Business Library'));
    return $this->sponsor ?: $default;
  }

  /**
   * @return string
   */
  public function getSponsorMessage() {
    $message = variable_get('gsb_connecting_link_sponsor_message', t('Paid and licensed for you by'));
    return format_string('@message @sponsor.', array('@message' => $message, '@sponsor' => $this->getSponsor()));
  }

  /**
   * @param string $alias
   *
   * @return \GSBConnectingLink|null
   */
  public static function loadByAlias($alias) {
    $query = db_select('gsb_connecting_link', 'cl');
    $query->fields('cl');
    $query->condition('alias', $alias);

    $links = $query->execute()->fetchAllAssoc('alias', 'GSBConnectingLink');
    return isset($links[$alias]) ? $links[$alias] : NULL;
  }

  /**
   * @param array $header
   *
   * @return \GSBConnectingLink[]
   */
  public static function loadFromPager($header) {
    $query = db_select('gsb_connecting_link', 'cl')->extend('TableSort')->extend('PagerDefault');
    $query->fields('cl');
    $query->orderByHeader($header);

    return $query->execute()->fetchAllAssoc('alias', 'GSBConnectingLink');
  }

}
