<?php

namespace Drupal\insumo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class addPersonal extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'labAguas2';
  }

  /**
   * Check form validation data
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['ID'] = [
      '#type' => 'number',
      '#title' => $this->t('ID de la Persona'),
      '#required' => TRUE,
    ];

    $form['nameP'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre Completo'),
      '#required' => TRUE,
    ];

    $form['DPI'] = [
      '#type' => 'number',
      '#title' => $this->t('DPI de la persona'),
      '#required' => TRUE,
    ];

    $form['phone'] = [
      '#type' => 'number',
      '#title' => $this->t('Numero de Telefono'),
      '#required' => TRUE,
    ];

    $form['cargo'] = [
      '#type' => 'number',
      '#title' => $this->t('Coloque el ID del cargo'),
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
    $conn->insert('Personal')->fields(
      array(
        'idPersonal' => $form_state->getValue('ID'),
        'NombrePersona' => $form_state->getValue('nameP'),
        'DPI' => $form_state->getValue('DPI'),
        'telefonoPersonal' => $form_state->getValue('phone'),
        'CargoPersonal_idCargoPersonal' => $form_state->getValue('cargo'),
      )
    )->execute();

    drupal_set_message(t('Persona agregada, Nombre: @nombre Cantidad: @id', 
      array(
        '@nombre' => $form_state->getValue('nameI'), 
        '@id' => $form_state->getValue('ID')
      )
    ));
  }

}
