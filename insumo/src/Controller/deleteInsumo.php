<?php

namespace Drupal\insumo\Controller;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class deleteInsumo extends FormBase {

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
      'idInsumo' => t('ID'),
      'nombre_insumo' => t('Nombre'),
      'cantidad' => t('Cantidad'),
      'Expiracion' => t('Caducidad'),
      'cantidadExixtente' => t('Existencia'),
    );

    $connection = Database::getConnection();
    $sth = $connection->select('Insumo', 'x')
        ->fields('x', array('idInsumo','nombre_insumo', 'cantidad','Expiracion','cantidadExixtente'));
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
        'idInsumo' => $data->idInsumo,
        'nombre_insumo' => $data->nombre_insumo,
        'cantidad' => $data->cantidad,
        'Expiracion' => $data->Expiracion,
        'cantidadExixtente' => $data->cantidadExixtente,
      );

      $codes[] = array(
        $data->idInsumo => t($data->idInsumo . ' -> ' . $data->nombre_insumo),
      );
    }

    $form['table'] = [
        '#type' => 'table',
        '#header' => $header_table,
        '#rows' => $rows,
        '#empty' => t('Insumo desconocido'),
    ];

    $form['type_options'] = array(
      '#type' => 'value',
      '#value' => $codes,
    );

    $form['ID'] = [
      '#type' => 'select',
      '#title' => t('Insumo a eliminar'),
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

  $erase = $conn->delete('Insumo')
    ->condition('idInsumo', $form_state->getValue('ID'))
    ->execute();

  drupal_set_message(t('Cargo eliminado ID: @id', 
    array(
      '@id' => $form_state->getValue('ID')
    )
  ));

  }

}
