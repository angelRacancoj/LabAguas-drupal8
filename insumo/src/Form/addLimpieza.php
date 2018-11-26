<?php

namespace Drupal\insumo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class addLimpieza extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'labAguas3';
  }

  /**
   * Check form validation data
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['ID'] = [
      '#type' => 'number',
      '#title' => $this->t('ID limpieza'),
      '#required' => TRUE,
    ];

    $form['desc'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Descripcion Limpieza'),
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
    $conn->insert('Limpieza')->fields(
      array(
        'idLimpieza' => $form_state->getValue('ID'),
        'descripcion_limpieza' => $form_state->getValue('desc'),
      )
    )->execute();

    drupal_set_message(t('Persona agregada, ID: @id Descripcion: @descrip', 
      array(
        '@descrip' => $form_state->getValue('desc'), 
        '@id' => $form_state->getValue('ID')
      )
    ));
  }

}
