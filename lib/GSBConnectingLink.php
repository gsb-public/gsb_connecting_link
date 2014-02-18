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
  public function getType() {
    $options = $this->getTypeOptions();
    return $options[$this->type];
  }
  public function delete() {
    db_delete('gsb_connecting_link')
      ->condition('clid', $this->clid)
      ->execute();
  }
  public function getSponsor() {
    $default = 'GSB';
    $message = 'This is sponsored by';
    $sponsor = $this->sponsor ?: $default;
    return t('@message @sponsor.', array('@message' => $message, '@sponsor' => $sponsor));
  }

  /**
   * @param int $clid
   *
   * @return \GSBConnectingLink
   */
  public static function load($clid) {
    $query = db_select('gsb_connecting_link', 'cl');
    $query->fields('cl');
    $query->condition('clid', $clid);

    $links = $query->execute()->fetchAllAssoc('clid', 'GSBConnectingLink');
    return $links[$clid];
  }

  /**
   * @param string $alias
   *
   * @return \GSBConnectingLink
   */
  public static function loadByAlias($alias) {
    $query = db_select('gsb_connecting_link', 'cl');
    $query->fields('cl');
    $query->condition('alias', $alias);

    $links = $query->execute()->fetchAllAssoc('alias', 'GSBConnectingLink');
    return $links[$alias];
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

    return $query->execute()->fetchAllAssoc('clid', 'GSBConnectingLink');
  }

}
