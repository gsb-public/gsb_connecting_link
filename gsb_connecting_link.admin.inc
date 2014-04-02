<?php

/**
 * Page callback for the connecting link overview.
 */
function gsb_connecting_link_overview() {
  $build = array();

  $header = array(
    'name' => array('data' => t('Name'), 'field' => 'name', 'sort' => 'asc'),
    'alias' => array('data' => t('Alias'), 'field' => 'alias'),
    'destination' => array('data' => t('Destination'), 'field' => 'destination'),
    'sponsor' => array('data' => t('Sponsor'), 'field' => 'sponsor'),
    'type' => array('data' => t('Type'), 'field' => 'type'),
    'operations' => array('data' => t('Operations')),
  );

  $links = \GSBConnectingLink::loadFromPager($header);
  $rows = array();
  foreach ($links as $link) {
    $row = array();
    $row['name'] = $link->name;
    $row['type'] = $link->getType();
    $row['alias'] = l($link->alias, 'library/articles/' . $link->type . '/links/' . $link->alias);
    $row['destination'] = l($link->destination, $link->destination);
    $row['sponsor'] = $link->getSponsor();

    $operations['edit'] = array(
      'title' => t('Edit'),
      'href' => 'admin/config/gsb/connecting-link/manage/' . $link->alias . '/' . $link->type,
    );
    $operations['delete'] = array(
      'title' => t('Delete'),
      'href' => 'admin/config/gsb/connecting-link/manage/' . $link->alias . '/' . $link->type . '/delete',
    );
    $row['operations'] = array(
      'data' => array(
        '#theme' => 'links',
        '#links' => $operations,
        '#attributes' => array('class' => array('links', 'inline', 'nowrap')),
      ),
    );
    $rows[] = $row;
  }
  $build['table'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#empty' => t('No connecting links available.'),
  );
  $build['pager'] = array('#theme' => 'pager');
  return $build;
}

/**
 * Form constructor for the GSB Connecting Link settings form.
 */
function gsb_connecting_link_settings_form($form, &$form_state) {
  $form['gsb_connecting_link_redirect_delay'] = array(
    '#title' => t('Redirect delay'),
    '#type' => 'select',
    '#options' => range(0, 15),
    '#default_value' => variable_get('gsb_connecting_link_redirect_delay', 3),
    '#field_suffix' => t('seconds'),
  );
  $form['gsb_connecting_link_sponsor_default'] = array(
    '#title' => t('Default sponsor name'),
    '#description' => t('This will be used if no sponsor is entered for a link.'),
    '#type' => 'textfield',
    '#default_value' => variable_get('gsb_connecting_link_sponsor_default', t('Graduate School of Business Library')),
  );
  $form['gsb_connecting_link_sponsor_message'] = array(
    '#title' => t('Sponsor message'),
    '#description' => t('This will be prepended to the sponsor name.'),
    '#type' => 'textfield',
    '#default_value' => variable_get('gsb_connecting_link_sponsor_message', t('Paid and Licensed for you by')),
  );
  return system_settings_form($form);
}

/**
 * Page callback for adding a new connecting link.
 */
function gsb_connecting_link_add_page() {
  drupal_set_title(t('Add new connecting link'));
  $link = new \GSBConnectingLink();
  return drupal_get_form('gsb_connecting_link_form', $link);
}

/**
 * Form constructor for the connecting link form.
 */
function gsb_connecting_link_form($form, &$form_state, \GSBConnectingLink $link) {
  $form['is_new'] = array(
    '#type' => 'value',
    '#value' => $link->isNew(),
  );
  $form['name'] = array(
    '#title' => t('Name'),
    '#description' => t('The name of this link. For administrative purposes only.'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#default_value' => $link->name,
  );
  $form['type'] = array(
    '#title' => t('Type'),
    '#description' => t('The type of this link. For administrative purposes only.'),
    '#type' => 'select',
    '#options' => $link->getTypeOptions(),
    '#required' => TRUE,
    '#default_value' => $link->type,
  );
  $form['alias'] = array(
    '#title' => t('Alias'),
    '#type' => 'machine_name',
    '#description' => t('A unique link alias. Can only contain lowercase letters, numbers, and hyphens. This cannot be changed after saving.'),
    '#machine_name' => array(
      'source' => FALSE,
      'replace_pattern' => '[^a-z0-9-]+',
      'replace' => '-',
      'exists' => 'gsb_connecting_link_exists',
    ),
    '#required' => TRUE,
    '#default_value' => $link->alias,
    '#disabled' => !$link->isNew(),
  );
  $form['destination'] = array(
    '#title' => t('Destination'),
    '#description' => t("An absolute URL, including 'http://'."),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#default_value' => $link->destination,
    '#element_validate' => array('gsb_connecting_link_destination_validate'),
  );
  $default_sponsor = variable_get('gsb_connecting_link_sponsor_default', t('Graduate School of Business Library'));
  $form['sponsor'] = array(
    '#title' => t('Sponsor'),
    '#description' => t("The sponsor for this link. Defaults to '@name'.", array('@name' => $default_sponsor)),
    '#type' => 'textfield',
    '#default_value' => $link->sponsor,
  );
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save link'),
  );
  return $form;
}

/**
 * Callback for form_validate_machine_name().
 *
 * @param string $value
 *   The value of the element being validated.
 * @param array $element
 *   An associative array representing the element being validated.
 * @param array $form_state
 *   An associative array containing the current state of the form.
 *
 * @return bool
 *   TRUE if the link exists, FALSE otherwise.
 */
function gsb_connecting_link_exists($value, $element, $form_state) {
  return (bool) \GSBConnectingLink::loadByAlias($value, $form_state['values']['type']);
}

/**
 * #element_validate callback: Ensures the destination is absolute.
 */
function gsb_connecting_link_destination_validate(&$element, &$form_state, $complete_form) {
  if (!url_is_external($element['#value'])) {
    form_error($element, t('The destination must be an absolute URL.'));
  }
}

/**
 * Form submission handler for gsb_connecting_link_form().
 */
function gsb_connecting_link_form_submit(&$form, &$form_state) {
  if (!empty($form_state['values']['is_new'])) {
    drupal_write_record('gsb_connecting_link', $form_state['values']);
    drupal_set_message(t('Connecting link %name has been added', array('%name' => $form_state['values']['name'])));
  }
  else {
    drupal_write_record('gsb_connecting_link', $form_state['values'], array('alias'));
    drupal_set_message(t('Connecting link %name has been updated', array('%name' => $form_state['values']['name'])));
  }
  $form_state['redirect'] = 'admin/config/gsb/connecting-link';
}

/**
 * Form constructor for the connecting link delete form.
 */
function gsb_connecting_link_delete_form($form, &$form_state, \GSBConnectingLink $link) {
  $form_state['storage']['gsb_connecting_link'] = $link;
  return confirm_form($form, t('Are you sure you want to delete the connecting link %name?', array('%name' => $link->name)), 'admin/config/gsb/connecting-link');
}

/**
 * Form submission handler for gsb_connecting_link_delete_form().
 */
function gsb_connecting_link_delete_form_submit(&$form, &$form_state) {
  $link = $form_state['storage']['gsb_connecting_link'];
  drupal_set_message(t('Connecting link %name has been deleted', array('%name' => $link->name)));
  $link->delete();
  $form_state['redirect'] = 'admin/config/gsb/connecting-link';
}
