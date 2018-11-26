<?php

namespace Drupal\insumo\Controller;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class deleteCargo extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'labAguas1';
  }

  /**
   * Check form validation data
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $header_table = array(
      'idCargoPersonal'=>    t('ID'),
      'NombreCargo' => t('Nombre'),
    );

    $connection = Database::getConnection();
    $sth = $connection->select('CargoPersonal', 'x')
        ->fields('x', array('idCargoPersonal', 'NombreCargo'));
    $data = $sth->execute();
    $results = $data->fetchAll();

    // Iterate results
    $rows=array();
    $codes = array();
    $codes[] = array(
        '' => t('--NONE--'),
      );
    foreach ($results as $data) {
      $rows[] = array(
        'idCargoPersonal' => $data->idCargoPersonal,
        'NombreCargo' => $data->NombreCargo,
      );

      $codes[] = array(
        $data->idCargoPersonal => t($data->idCargoPersonal . ' -> ' . $data->NombreCargo),
      );
    }

    $form['table'] = [
        '#type' => 'table',
        '#header' => $header_table,
        '#rows' => $rows,
        '#empty' => t('Cargo desconocido'),
    ];

    $form['type_options'] = array(
      '#type' => 'value',
      '#value' => $codes,
    );

    $form['ID'] = [
      '#type' => 'select',
      '#title' => t('Cargo a eliminar'),
      '#options' => $form['type_options']['#value'],
      '#required' => TRUE,
      '#default_value' => '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Eliminar'),
    ];

    return $form;

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  $conn = Database::getConnection();

  $erase = $conn->delete('CargoPersonal')
    ->condition('idCargoPersonal', $form_state->getValue('ID'))
    ->execute();

  drupal_set_message(t('Cargo eliminado ID: @id', 
    array(
      '@id' => $form_state->getValue('ID')
    )
  ));

  }

}
