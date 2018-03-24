<?php

namespace Drupal\sigfox_migrate\Plugin\migrate\source;

use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\migrate\Row;

/**
 * Source plugin for retrieving data via URLs.
 *
 * @MigrateSource(
 *   id = "sigfoxurl"
 * )
 */
class SigfoxUrl extends Url {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if ($row->hasSourceProperty('data')) {
      $data = $row->getSourceProperty('data');

      // Decode data.
      // @See: Project readme.
      $gps = unpack("flat/flong", hex2bin($data));

      if (isset($gps['lat']) &&
        isset($gps['long']) &&
        $gps['lat'] != 0 &&
        $gps['long'] != 0) {
        $row->setSourceProperty('data_lat', $gps['lat']);
        $row->setSourceProperty('data_long', $gps['long']);
      }
      else {
        $row->setSourceProperty('data_lat', '');
        $row->setSourceProperty('data_long', '');
      }
    }
    return parent::prepareRow($row);
  }
}
