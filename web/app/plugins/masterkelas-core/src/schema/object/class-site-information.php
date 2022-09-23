<?php

namespace MasterKelas\Schema\Object;

/**
 * Site Information object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class SiteInformation {
  public static function register() {
    self::anchor_link_type();
    self::links_column_type();
    self::site_information_type();
  }

  public static function anchor_link_type() {
    register_graphql_object_type("AnchorLink", [
      "description" => "Anchor Link",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "String"],
          "description" => "Link id",
        ],
        "name" => [
          "type" => ["non_null" => "String"],
          "description" => "Link name",
        ],
        "dest" => [
          "type" => ["non_null" => "String"],
          "description" => "Link destination",
        ],
        "title" => [
          "type" => "String",
          "description" => "Link title",
        ],
        "target" => [
          "type" => "String",
          "description" => "Link target",
        ],
      ],
    ]);
  }

  public static function links_column_type() {
    register_graphql_object_type("LinksColumn", [
      "description" => "Links Column Link",
      "fields" => [
        "name" => [
          "type" => ["non_null" => "String"],
          "description" => "Column name",
        ],
        "links" => [
          "type" => ["list_of" => "AnchorLink"],
          "description" => "Column links",
        ],
      ],
    ]);
  }

  public static function site_information_type() {
    register_graphql_object_type("SiteInformation", [
      "description" => "Site Information",
      "fields" => [
        "maintenance" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Is maintenance mode activated?",
        ],
        "playerProvider" => [
          "type" => ["non_null" => "String"],
          "description" => "Current active player provider",
        ],
        "playerData" => [
          "type" => "String",
          "description" => "Player data and configurations in JSON format",
        ],
        "headerLinks" => [
          "type" => ["list_of" => "AnchorLink"],
          "description" => "Header links",
        ],
        "footerLinks" => [
          "type" => ["list_of" => "LinksColumn"],
          "description" => "Footer columns",
        ],
        "appsLinks" => [
          "type" => ["list_of" => "AnchorLink"],
          "description" => "Apps links",
        ],
        "socialMediasLinks" => [
          "type" => ["list_of" => "AnchorLink"],
          "description" => "Social medias accounts",
        ],
      ],
    ]);
  }
}
