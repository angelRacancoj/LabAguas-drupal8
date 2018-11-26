<?php

namespace Drupal\insumo\Controller;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class deletePersonal extends FormBase {

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
      'idPersonal' => t('ID'),
      'NombrePersona' => t('Nombre'),
      'DPI' => t('DPI'),
      'telefonoPersonal' => t('Telefono'),
      'Cargo' => t('Cargo'),
    );

    $connection = Database::getConnection();
    $sth = $connection->select('Personal', 'x')
        ->fields('x', array('idPersonal','NombrePersona','DPI','telefonoPersonal','CargoPersonal_idCargoPersonal'));
    $data = $sth->execute();
    $results = $data->fetchAll();

    // Iterate results
    $rows=array();
    $codes = array();
    $codes[] = array(
        '' => t('--NONE--'),
      );
    foreach ($results as $data) {
/*
      $connect = Database::getConnection();

      $cargoN = $connect->select('CargoPersonal', 'x')
        ->fields('x', array('NombreCargo'))
        ->condition('x.idCargoPersonal', $form_state->getValue($data->CargoPersonal_idCargoPersonal) , '=')
        ->execute()->fetchAll();

        $cargoOut = 'null';
        foreach ($cargoN as $cargoNom) {
          $cargoOut = $cargoNom->NombreCargo;
        }
*/
      $rows[] = array(
        'idPersonal' => $data->idPersonal,
        'NombrePersona' => $data->NombrePersona,
        'DPI' => $data->DPI,
        'telefonoPersonal' => $data->telefonoPersonal,
        'Cargo' => $data->CargoPersonal_idCargoPersonal,
      );

      $codes[] = array(
        $data->idPersonal => t($data->idPersonal . ' -> ' . $data->NombrePersona),
      );
    }

    $form['table'] = [
        '#type' => 'table',
        '#header' => $header_table,
        '#rows' => $rows,
        '#empty' => t('Personal desconocido'),
    ];

    $form['type_options'] = array(
      '#type' => 'value',
      '#value' => $codes,
    );

    $form['ID'] = [
      '#type' => 'select',
      '#title' => t('Personal a eliminar'),
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

  $erase = $conn->delete('Personal')
    ->condition('idPersonal', $form_state->getValue('ID'))
    ->execute();

  drupal_set_message(t('Personal eliminado ID: @id', 
    array(
      '@id' => $form_state->getValue('ID')
    )
  ));

  }

}
