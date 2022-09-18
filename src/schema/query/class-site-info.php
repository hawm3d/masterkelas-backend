<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Site Info query
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class SiteInfo {
  public static function register() {
    self::site_info();
  }

  public static function site_info() {
    Schema::query(
      'RootQuery',
      "siteInfo",
      [
        "type" => ["non_null" => "SiteInformation"],
        "description" => "Site Information",
        "resolve" => function ($_, $__, MasterContext $context) {
          return [
            'maintenance' => (bool) false,
            'playerProvider' => (string) "mk-player",
            'playerData' => null,
            'headerLinks' => [
              [
                "id" => "link-name",
                "name" => "Link Name",
                "dest" => "/",
                "title" => "Link Title",
              ]
            ],
            'footerLinks' => [
              [
                "name" => "دسترسی سریع",
                "links" => [
                  [
                    "id" => "footer-link-1",
                    "name" => "لینک فوتر 1",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 1",
                  ],
                  [
                    "id" => "footer-link-2",
                    "name" => "لینک فوتر 2",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 2",
                  ],
                  [
                    "id" => "footer-link-3",
                    "name" => "لینک فوتر 3",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 3",
                  ],
                  [
                    "id" => "footer-link-4",
                    "name" => "لینک فوتر 4",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 4",
                  ],
                ]
              ],
              [
                "name" => "درباره مسترکلاس",
                "links" => [
                  [
                    "id" => "footer-link-5",
                    "name" => "لینک فوتر 5",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 5",
                  ],
                  [
                    "id" => "footer-link-6",
                    "name" => "لینک فوتر 6",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 6",
                  ],
                  [
                    "id" => "footer-link-7",
                    "name" => "لینک فوتر 7",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 7",
                  ],
                  [
                    "id" => "footer-link-8",
                    "name" => "لینک فوتر 8",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 8",
                  ],
                ]
              ],
              [
                "name" => "پشتیبانی",
                "links" => [
                  [
                    "id" => "footer-link-9",
                    "name" => "لینک فوتر 9",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 9",
                  ],
                  [
                    "id" => "footer-link-10",
                    "name" => "لینک فوتر 10",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 10",
                  ],
                  [
                    "id" => "footer-link-11",
                    "name" => "لینک فوتر 11",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 11",
                  ],
                  [
                    "id" => "footer-link-12",
                    "name" => "لینک فوتر 12",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 12",
                  ],
                  [
                    "id" => "footer-link-13",
                    "name" => "لینک فوتر 13",
                    "dest" => "/",
                    "title" => "عنوان لینک فوتر 13",
                  ],
                ]
              ],
            ],
            'appsLinks' => [],
            'socialMediasLinks' => [
              [
                "id" => "instagram",
                "name" => "Instagram",
                "dest" => "https://instagram.com",
                "title" => "Instagram Title",
              ]
            ],
          ];
        }
      ],
    );
  }
}
