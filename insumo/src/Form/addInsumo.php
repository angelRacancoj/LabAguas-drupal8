<?php

namespace Drupal\insumo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class addInsumo extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'labAguas';
  }

  /**
   * Check form validation data
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $date_format = 'Y-m-d';

    $form['nameI'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre Insumo'),
      '#required' => TRUE,
    ];

    $form['cant'] = [
      '#type' => 'number',
      '#title' => $this->t('Cantidad'),
      '#required' => TRUE,
    ];

    $form['expiracion'] = [
      '#type' => 'date',
      '#title' => $this->t('Fecha de caducidad'),
      '#required' => TRUE,
      '#date_date_format' => $date_format,    
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
    $conn->insert('Insumo')->fields(
      array(
        'nombre_insumo' => $form_state->getValue('nameI'),
        'cantidad' => $form_state->getValue('cant'),
        'Expiracion' => $form_state->getValue('expiracion'),
        'cantidadExixtente' => $form_state->getValue('cant'),
      )
    )->execute();

    drupal_set_message(t('Persona agregada, Nombre: @nombre Cantidad: @id, Caduca: @fecha', 
      array(
        '@nombre' => $form_state->getValue('nameI'), 
        '@id' => $form_state->getValue('cant'),
        '@fecha' => $form_state->getValue('expiracion'),
      )
    ));
  }

}
