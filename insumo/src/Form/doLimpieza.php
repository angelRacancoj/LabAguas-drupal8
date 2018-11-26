<?php

namespace Drupal\insumo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class doLimpieza extends FormBase {

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

     $connection = Database::getConnection();

    $header_persona = array(
      'idPersonal' => t('ID'),
      'NombrePersona' => t('Nombre'),
      'DPI' => t('DPI'),
      'telefonoPersonal' => t('Telefono'),
      'CargoPersonal_idCargoPersonal' => t('Cargo'),
    );

    $header_limpieza = array(
      'idLimpieza' => t('ID'),
      'descripcion_limpieza' => t('Descripcion'),
    );

    /*
    * Coneccion para extraer la tabla y los valores para el comboBox para Insumo
    */
    $sthPersonal = $connection->select('Personal', 'x')
        ->fields('x', array('idPersonal','NombrePersona', 'DPI','telefonoPersonal','CargoPersonal_idCargoPersonal'));
    $dataPersonal = $sthPersonal->execute();
    $resultsInsumo = $dataPersonal->fetchAll();

    $rowsPersonal = array();
    $codesPersonal = array();
    $codesPersonal[] = array('' => t('--NONE--'),);
    foreach ($resultsInsumo as $dataPersonal) {
      $rowsPersonal[] = array(
        'idPersonal' => $dataPersonal->idPersonal,
        'NombrePersona' => $dataPersonal->NombrePersona,
        'DPI' => $dataPersonal->DPI,
        'telefonoPersonal' => $dataPersonal->telefonoPersonal,
        'CargoPersonal_idCargoPersonal' => $dataPersonal->CargoPersonal_idCargoPersonal,
      );
      $codesPersonal[] = array($dataPersonal->idPersonal => t($dataPersonal->idPersonal . ' -> ' . $dataPersonal->NombrePersona),);
    }

    $form['personal_table'] = [
        '#type' => 'table',
        '#header' => $header_persona,
        '#rows' => $rowsPersonal,
        '#empty' => t('Personal desconocida'),
    ];

    /*
    * Coneccion para extraer la tabla y los valores para el comboBox para Almacen
    */
    $sthLimpieza = $connection->select('Limpieza', 'y')
        ->fields('y', array('idLimpieza','descripcion_limpieza'));
    $dataLimpieza = $sthLimpieza->execute();
    $resultsAlmacen = $dataLimpieza->fetchAll();

    $rowsLimpieza=array();
    $codesLimpieza = array();
    $codesLimpieza[] = array('' => t('--NONE--'),);
    foreach ($resultsAlmacen as $dataLimpieza) {
      $rowsLimpieza[] = array(
        'idLimpieza' => $dataLimpieza->idLimpieza,
        'descripcion_limpieza' => $dataLimpieza->descripcion_limpieza,
      );
      $codesLimpieza[] = array($dataLimpieza->idLimpieza => t($dataLimpieza->idLimpieza),);
    }

    $form['limpieza_table'] = [
        '#type' => 'table',
        '#header' => $header_limpieza,
        '#rows' => $rowsLimpieza,
        '#empty' => t('Cargo desconocido'),
    ];

    $form['type_ops_personal'] = array(
      '#type' => 'value',
      '#value' => $codesPersonal,
    );

    $form['personalID'] = [
      '#type' => 'select',
      '#title' => t('ID Personal'),
      '#options' => $form['type_ops_personal']['#value'],
      '#required' => TRUE,
      '#default_value' => '',
    ];

    $form['type_ops_limpieza'] = array(
      '#type' => 'value',
      '#value' => $codesLimpieza,
    );

    $form['limpiezaID'] = [
      '#type' => 'select',
      '#title' => t('ID Limpieza'),
      '#options' => $form['type_ops_limpieza']['#value'],
      '#required' => TRUE,
      '#default_value' => '',
    ];

    $date_format = 'Y-m-d';

    $form['fecha'] = [
      '#type' => 'date',
      '#title' => $this->t('Fecha de realizacion'),
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
    $conn->insert('Limpiar')->fields(
      array(
        'Personal_idPersonal' => $form_state->getValue('personalID'),
        'Limpieza_idLimpieza' => $form_state->getValue('limpiezaID'),
        'fecha_limpieza' => $form_state->getValue('fecha'),
      )
    )->execute();

    drupal_set_message(t('Limpieza realizada por, ID: @id Tipo: @descrip, la fecha: @fecha', 
      array(
        '@descrip' => $form_state->getValue('limpiezaID'), 
        '@id' => $form_state->getValue('personalID'),
        '@fecha' => $form_state->getValue('fecha')
      )
    ));
  }

}
