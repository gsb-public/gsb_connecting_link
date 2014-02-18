<?php

/**
 * @file
 * Contains \GSBConnectingLink.
 */

/**
 * Class GSBConnectingLink.
 */
class GSBConnectingLink {
  public $clid;
  public $name;
  public $alias;
  public $destination;
  public $sponsor;
  public $type;
  public function getTypeOptions() {
    return array(
      'ejournal' => t('eJournal'),
      'database' => t('Database'),
      'ebook' => t('eBook'),
    );
  }
  public function isNew() {
    return empty($this->alias);
  }
  public function getType() {
    $options = $this->getTypeOptions();
    return $options[$this->type];
  }
  public function delete() {
    db_delete('gsb_connecting_link')
      ->condition('alias', $this->alias)
      ->execute();
  }
  public function getSponsor() {
    $default = 'GSB';
    return $this->sponsor ?: $default;
  }
  public function getSponsorMessage() {
    $message = 'This is sponsored by';
    return t('@message @sponsor.', array('@message' => $message, '@sponsor' => $this->getSponsor()));
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
