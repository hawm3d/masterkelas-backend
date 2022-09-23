<?php

namespace MasterKelas;

use Jenssegers\Optimus\Optimus;

/**
 * Generate Optimus Id
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OptimusId {
  private $optimus;

  public static function encode(Int $id) {
    return (new self())->optimus->encode($id);
  }

  public static function decode(Int $id) {
    return (new self())->optimus->decode($id);
  }

  public static function course() {
    return (new self(2093139821, 1129798245, 603506317, 31))->optimus;
  }

  public static function course_lesson() {
    return (new self(2050435813, 2000782061, 1367918099, 31))->optimus;
  }

  public static function course_category() {
    return (new self(1119130319, 1783182383, 1733488917, 31))->optimus;
  }

  public static function course_tag() {
    return (new self(1734399967, 2053481503, 1793789235, 31))->optimus;
  }

  public static function subscription() {
    return (new self(1726621493, 2118267677, 996367569, 31))->optimus;
  }

  public function __construct(
    int $prime = 2048824403,
    int $inverse = 1031327707,
    int $xor = 1154672441,
    int $size = 31
  ) {
    $this->optimus = new Optimus($prime, $inverse, $xor, $size);
  }
}
