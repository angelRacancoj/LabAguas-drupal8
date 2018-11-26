<?php

namespace Drupal\insumo\Controller;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class deleteLimpieza extends FormBase {

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
      'idLimpieza'=>t('ID'),
      'descripcion_limpieza' => t('Nombre'),
    );

    $connection = Database::getConnection();
    $sth = $connection->select('Limpieza', 'x')
        ->fields('x', array('idLimpieza', 'descripcion_limpieza'));
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
        'idLimpieza' => $data->idLimpieza,
        'descripcion_limpieza' => $data->descripcion_limpieza,
      );

      $codes[] = array(
        $data->idLimpieza => t($data->idLimpieza),
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

  $erase = $conn->delete('Limpieza')
    ->condition('idLimpieza', $form_state->getValue('ID'))
    ->execute();

  drupal_set_message(t('Limpieza eliminada ID: @id', 
    array(
      '@id' => $form_state->getValue('ID')
    )
  ));

  }

}
