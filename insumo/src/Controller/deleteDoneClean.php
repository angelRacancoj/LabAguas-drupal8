<?php

namespace Drupal\insumo\Controller;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class deleteDoneClean extends FormBase {

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
      'idlimpiar' => t('ID'),
      'Personal_idPersonal' => t('ID Persona'),
      'Limpieza_idLimpieza' => t('ID Limpieza'),
      'fecha_limpieza' => t('Realizacion'),
    );

    $connection = Database::getConnection();
    $sth = $connection->select('limpiar', 'x')
        ->fields('x', array('idlimpiar','Personal_idPersonal', 'Limpieza_idLimpieza','fecha_limpieza'));
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
        'idlimpiar' => $data->idlimpiar,
        'Personal_idPersonal' => $data->Personal_idPersonal,
        'Limpieza_idLimpieza' => $data->Limpieza_idLimpieza,
        'fecha_limpieza' => $data->fecha_limpieza,
      );

      $codes[] = array(
        $data->idlimpiar => t('ID->' . $data->idlimpiar),
      );
    }

    $form['table'] = [
        '#type' => 'table',
        '#header' => $header_table,
        '#rows' => $rows,
        '#empty' => t('Limpieza desconocida'),
    ];

    $form['type_options'] = array(
      '#type' => 'value',
      '#value' => $codes,
    );

    $form['ID'] = [
      '#type' => 'select',
      '#title' => t('Limpieza a eliminar'),
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

  $erase = $conn->delete('limpiar')
    ->condition('idlimpiar', $form_state->getValue('ID'))
    ->execute();

  drupal_set_message(t('Cargo eliminado ID: @id', 
    array(
      '@id' => $form_state->getValue('ID')
    )
  ));

  }

}
