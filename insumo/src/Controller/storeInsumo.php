<?php

namespace Drupal\insumo\Controller;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Our simple form class.
 */
class storeInsumo extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'labAguas';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $connection = Database::getConnection();

    $header_insumo = array(
      'idInsumo' => t('ID'),
      'nombre_insumo' => t('Nombre'),
      'cantidad' => t('Cantidad'),
      'Expiracion' => t('Caducidad'),
      'cantidadExixtente' => t('Existencia'),
    );

    $header_almacen = array(
      'idAlmacen' => t('ID'),
      'nombreAlmacen' => t('Nombre'),
      'mts2Total' => t('Espacio total (mts2)'),
      'mts2Dsiponibles' => t('Espacio disponible (mts2)'),
    );

    /*
    * Coneccion para extraer la tabla y los valores para el comboBox para Insumo
    */
    $sthInsumo = $connection->select('Insumo', 'x')
        ->fields('x', array('idInsumo','nombre_insumo', 'cantidad','Expiracion','cantidadExixtente'));
    $dataInsumo = $sthInsumo->execute();
    $resultsInsumo = $dataInsumo->fetchAll();

    $rowsInsumo=array();
    $codesInsumo = array();
    $codesInsumo[] = array('' => t('--NONE--'),);
    foreach ($resultsInsumo as $dataInsumo) {
      $rowsInsumo[] = array(
        'idInsumo' => $dataInsumo->idInsumo,
        'nombre_insumo' => $dataInsumo->nombre_insumo,
        'cantidad' => $dataInsumo->cantidad,
        'Expiracion' => $dataInsumo->Expiracion,
        'cantidadExixtente' => $dataInsumo->cantidadExixtente,
      );
      $codesInsumo[] = array($dataInsumo->idInsumo => t($dataInsumo->idInsumo . ' -> ' . $dataInsumo->nombre_insumo),);
    }

    $form['insumo_table'] = [
        '#type' => 'table',
        '#header' => $header_insumo,
        '#rows' => $rowsInsumo,
        '#empty' => t('Cargo desconocido'),
    ];

    /*
    * Coneccion para extraer la tabla y los valores para el comboBox para Almacen
    */
    $sthAlmacen = $connection->select('Almacen', 'y')
        ->fields('y', array('idAlmacen','nombreAlmacen', 'mts2Total','mts2Dsiponibles'));
    $dataAlmacen = $sthAlmacen->execute();
    $resultsAlmacen = $dataAlmacen->fetchAll();

    $rowsAlmacen=array();
    $codesAlmacen = array();
    $codesAlmacen[] = array('' => t('--NONE--'),);
    foreach ($resultsAlmacen as $dataAlmacen) {
      $rowsAlmacen[] = array(
        'idAlmacen' => $dataAlmacen->idAlmacen,
        'nombreAlmacen' => $dataAlmacen->nombreAlmacen,
        'mts2Total' => $dataAlmacen->mts2Total,
        'mts2Dsiponibles' => $dataAlmacen->mts2Dsiponibles,
      );
      $codesAlmacen[] = array($dataAlmacen->idAlmacen => t($dataAlmacen->idAlmacen . ' -> ' . $dataAlmacen->nombreAlmacen),);
    }

    $form['insumo_almacen'] = [
        '#type' => 'table',
        '#header' => $header_almacen,
        '#rows' => $rowsAlmacen,
        '#empty' => t('Cargo desconocido'),
    ];

    $form['type_options_insumo'] = array(
      '#type' => 'value',
      '#value' => $codesInsumo,
    );

    $form['insID'] = [
      '#type' => 'select',
      '#title' => t('ID Insumo'),
      '#options' => $form['type_options_insumo']['#value'],
      '#required' => TRUE,
      '#default_value' => '',
    ];

    $form['type_options_almacen'] = array(
      '#type' => 'value',
      '#value' => $codesAlmacen,
    );

    $form['almID'] = [
      '#type' => 'select',
      '#title' => t('ID Almacen'),
      '#options' => $form['type_options_almacen']['#value'],
      '#required' => TRUE,
      '#default_value' => '',
    ];

    $form['space'] = [
      '#type' => 'number',
      '#title' => $this->t('Metros cuadrados que ocupa'),
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

    try {

      $cant = $conn->select('insumoAlmacen', 'z')
        ->fields('z', array('Insumo_idInsumo', 'Almacen_idAlmacen', 'espacioOcupado'))
        ->condition('z.Insumo_idInsumo', $form_state->getValue('insID') , '=')
        ->condition('z.Almacen_idAlmacen', $form_state->getValue('almID') , '=')
        ->countQuery()->execute()->fetchField();

      if ($cant == 0) {
        
        $query = $conn->update('Almacen')
          ->where('mts2Dsiponibles > :espacio', [':espacio' => $form_state->getValue('space')])
          ->where('idAlmacen = :idA', [':idA' => $form_state->getValue('almID')])
          ->expression('mts2Dsiponibles', 'mts2Dsiponibles - :espacio2', [':espacio2' => $form_state->getValue('space')]);
        $query_number = $query->execute();

        if ($query_number == 1) {
            $conn->insert('insumoAlmacen')->fields(
              array(
                'Insumo_idInsumo' => $form_state->getValue('insID'),
                'Almacen_idAlmacen' => $form_state->getValue('almID'),
                'espacioOcupado' => $form_state->getValue('space'),
              )
            )->execute();

            drupal_set_message(t('Insumo almacenado, ID Insumo: @insumoID, ID Almacen: @almacenID, Espacio ocupado: @espacio', 
              array(
                '@insumoID' => $form_state->getValue('insID'), 
                '@almacenID' => $form_state->getValue('IDalmacen'),
                '@espacio' => $form_state->getValue('space')
              )
            ));
        } else {
          drupal_set_message(t('El Almacen con el ID: @id no tiene espacio suficiente.', 
            array(
              '@id' => $form_state->getValue('ID'),
            )), 'error');
        }
      } else {

        drupal_set_message(t('El Insumo: @id, ya se encuentra almacenado en: @nombre.', 
          array(
            '@id' => $form_state->getValue('insID'),
            '@nombre' => $form_state->getValue('almID'),
          )), 'error');
      }
    } catch (Exception $e) {
      $conn->rollBack();
      \Drupal::logger('type')->error($e->getMessage());
    }
  }
}
