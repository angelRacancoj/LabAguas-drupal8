<?php

namespace Drupal\insumo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class addCargo extends FormBase {

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

    $form['ID'] = [
      '#type' => 'number',
      '#title' => $this->t('ID del Cargo'),
      '#required' => TRUE,
    ];

    $form['nameC'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre del Cargo'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Agregar'),
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $conn = Database::getConnection();

    $connection = Database::getConnection();
    $sth = $connection->select('CargoPersonal', 'x')
      ->fields('x', array('idCargoPersonal', 'NombreCargo'))
      ->condition('x.idCargoPersonal', $form_state->getValue('ID') , '=')
      ->countQuery()->execute()->fetchField();

    if ($sth == 0) {

      $conn->insert('CargoPersonal')->fields(
        array(
          'idCargoPersonal' => $form_state->getValue('ID'),
          'NombreCargo' => $form_state->getValue('nameC'),
        )
      )->execute();

      drupal_set_message(t('Cargo Agregado, Nombre: @nombre ID: @id', 
        array(
          '@nombre' => $form_state->getValue('nameC'), 
          '@id' => $form_state->getValue('ID')
        )
      ));
    } else {

      drupal_set_message(t('El ID: @id  con el nombre: @nombre es invalido.', 
        array(
          '@id' => $form_state->getValue('ID'),
          '@nombre' => $form_state->getValue('nameC'),
        )), 'error');

    }

    
  }

}
