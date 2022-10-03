<?php

namespace MasterKelas\Command;

use MasterKelas\Model\Course;

/**
 * Master Factory Command
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes/cache
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class MasterFactory {
  public $stats = [
    "created_categories" => 0,
    "failed_categories" => 0,
    "created_courses" => 0,
    "failed_courses" => 0,
    "created_lessons" => 0,
    "failed_lessons" => 0,
    "downloaded_images" => 0,
    "failed_images" => 0,
  ];

  public function posts() {
    $posts_data = [
      [
        "type" => "page",
        "slug" => "index",
        "title" => "صفحه اصلی",
        "description" => "",
        "after_save" => function ($id) {
          update_option('page_on_front', $id);
          update_option('show_on_front', 'page');
        }
      ],
      [
        "type" => "page",
        "slug" => "blog",
        "title" => "صفحه اصلی وبلاگ",
        "description" => "",
        "after_save" => function ($id) {
          update_option('page_for_posts', $id);
        }
      ],
      [
        "type" => "page",
        "slug" => "policies",
        "title" => "قوانین، ضوابط و شرایط",
        "description" => "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی ایجاد کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد نیاز شامل حروفچینی دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.",
        "after_save" => function ($id) {
          update_option('woocommerce_terms_page_id', $id);
          \MasterKelas\Admin::set_option("policy-page", $id);
        }
      ],
    ];
    $created_posts = 0;
    $failed_posts = 0;

    \WP_CLI::log(
      sprintf(
        "Inserting %s Posts...",
        count($posts_data)
      )
    );

    try {
      foreach ($posts_data as $post_data) {
        $this->delog($post_data, "posts", "var_export");
        if ($post_data['type'] === 'page') {
          $post_id = 0;
          $post = get_page_by_path($post_data['slug']);
          if (!$post) {
            $post_id = wp_insert_post([
              'comment_status' => 'close',
              'ping_status' => 'close',
              'post_author' => 1,
              "post_status" => 'publish',
              "post_title" => $post_data['title'],
              "post_name" => $post_data['slug'],
              "post_content" => $post_data['description'],
              "post_type" => $post_data['type'],
            ]);
          } else {
            $post_id = $post->ID;
          }

          $this->delog($post_id, "posts");

          if ($post_id && !is_wp_error($post_id) && $post_id > 0) {
            if (!$post)
              $created_posts++;

            if (isset($post_data['after_save']) && is_callable($post_data['after_save']))
              $post_data['after_save']($post_id);
          }
        }
      }
    } catch (\Throwable $th) {
      $this->delog($th, "posts", false);
      $failed_posts++;
    }

    \WP_CLI::log(
      sprintf(
        "Posts: %s total | %s inserted | %s failed",
        count($posts_data),
        $created_posts,
        $failed_posts,
      )
    );
  }

  public function run() {
    try {
      \WP_CLI::log(
        sprintf(
          "Inserting %s Categories...",
          count($this->categories_data)
        )
      );
      $this->categories();

      \WP_CLI::log(
        sprintf(
          "Inserting %s Courses...",
          count($this->courses_data)
        )
      );
      $this->courses();

      \WP_CLI::log(
        "Factory Finished."
      );

      \WP_CLI::log(
        sprintf(
          "Categories: %s total | %s inserted | %s failed",
          count($this->categories_data),
          $this->stats['created_categories'],
          $this->stats['failed_categories'],
        )
      );

      \WP_CLI::log(
        sprintf(
          "Courses: %s total | %s inserted | %s failed",
          count($this->courses_data),
          $this->stats['created_courses'],
          $this->stats['failed_courses'],
        )
      );

      \WP_CLI::log(
        sprintf(
          "Lessons: %s inserted | %s failed",
          $this->stats['created_lessons'],
          $this->stats['failed_lessons'],
        )
      );

      \WP_CLI::log(
        sprintf(
          "Images: %s inserted | %s failed",
          $this->stats['downloaded_images'],
          $this->stats['failed_images'],
        )
      );
    } catch (\Throwable $th) {
      $this->delog($th, "run", false);
    }
  }

  public function reset() {
    try {
      $this->reset_factory();
    } catch (\Throwable $th) {
      $this->delog($th, "reset", false);
    }
  }

  private function reset_factory() {
    $deleted_categories = 0;
    $deleted_courses = 0;
    $deleted_lessons = 0;
    $deleted_images = 0;
    $attachment_ids = [];

    foreach ($this->courses_data as $course_data) {
      $this->delog($course_data, "reset_courses", "var_export");
      $slug = $course_data['slug'] ?: sanitize_title($course_data['title']);
      $this->delog($slug, "reset_courses");

      try {
        $course = Course::get_course_by_slug($slug);
        $course_lessons = learndash_get_course_lessons_list($course, "");
        if (!empty($course_lessons)) {
          foreach ($course_lessons as $lesson) {
            if ($poster_id = learndash_get_setting($lesson['id'], "mk_lesson_poster")) {
              $attachment_ids[] = $poster_id;
            }

            if (wp_delete_post($lesson['id'], true)) {
              $deleted_lessons++;
            } else {
              $this->delog(["Error: cant delete lesson.", $lesson], "reset_lessons");
            }
          }
        } else {
          $this->delog("Lessons empty.", "reset_lessons");
        }

        $imgs = [
          'mk_book_img',
          'mk_profile_img',
          'mk_portrait_img',
          'mk_typography_img',
          'mk_cinematic_img',
          'mk_landscape_img',
        ];

        foreach ($imgs as $img_key) {
          if ($img_id = learndash_get_setting($course, $img_key)) {
            $attachment_ids[] = $img_id;
          }
        }

        if (wp_delete_post($course->ID, true)) {
          $deleted_courses++;
        } else {
          $this->delog(["Error: cant delete course.", $course], "reset_courses");
        }
      } catch (\Throwable $th) {
        $this->delog($th, "reset_courses", false);
        continue;
      }
    }

    foreach ($this->categories_data as $cat) {
      if (!term_exists($cat['slug'], "ld_course_category")) {
        $this->delog(["Error: category doesnt exists", $course], "reset_categories");
        continue;
      }

      $term = get_term_by("slug", $cat['slug'], "ld_course_category");
      if ($term && !is_wp_error($term)) {
        $deleted = wp_delete_term($term->term_id, 'ld_course_category');
        if ($deleted && !is_wp_error($deleted)) {
          $deleted_categories++;
        } else {
          $this->delog(["Error: deleted is_wp_error", $deleted, $term], "reset_categories");
        }
      } else {
        $this->delog(["Error: term is_wp_error", $term], "reset_categories");
      }
    }

    if (!empty($attachment_ids)) {
      foreach ($attachment_ids as $id) {
        if (wp_delete_attachment(intval($id), true)) {
          $deleted_images++;
        } else {
          $this->delog(["Error: cant delete attachment", $id], "reset_images");
        }
      }
    } else {
      $this->delog("Empty attachments", "reset_images");
    }

    \WP_CLI::log(
      sprintf(
        "Deleted: %s categories | %s courses | %s lessons | %s images",
        $deleted_categories,
        $deleted_courses,
        $deleted_lessons,
        $deleted_images,
      )
    );
  }

  private function categories() {
    foreach ($this->categories_data as $category) {
      $this->delog($category, "categories", "var_export");
      if (isset($category['slug'])) {
        $this->delog($category['slug'], "categories");
      }

      if (
        !isset($category['slug'], $category['title']) ||
        term_exists($category['slug'], 'ld_course_category')
      ) {
        $this->delog(["Error: Bad props or category exists.", $category['slug'] ?? term_exists($category['slug'], 'ld_course_category')], "categories");
        $this->stats['failed_categories']++;
        continue;
      }

      $cat_id = wp_insert_term(
        $category['title'],
        'ld_course_category',
        [
          "slug" => $category['slug']
        ]
      );

      if (!is_wp_error($cat_id)) {
        $this->stats['created_categories']++;
      } else {
        $this->delog(["Error: cat_id is_wp_error", $cat_id], "categories");
        $this->stats['failed_categories']++;
      }
    }
  }

  private function lessons($lessons, $course_id) {
    foreach ($lessons as $key => $lesson) {
      $this->delog($lesson, "lessons", "var_export");
      $slug = $lesson['slug'] ?: sanitize_title($lesson['title']);
      $this->delog("Inserting Lesson #" . ($key + 1), "lessons");
      $lesson_id = wp_insert_post([
        "post_type" => learndash_get_post_type_slug('lesson'),
        "post_title" => $lesson['title'],
        "post_author" => 1,
        "post_name" => $slug,
        "post_content" => $lesson['description'] ?: null,
        "post_status" => 'publish',
        "meta_input" => [
          "course_id" => $course_id
        ]
      ]);

      if (!$lesson_id || is_wp_error($lesson_id)) {
        $this->delog(["Error: lesson_id is_wp_error", $lesson_id, $course_id], "lessons");
        $this->stats['failed_lessons']++;
        continue;
      }

      $this->stats['created_lessons']++;
      $this->update_setting($lesson_id, "course", $course_id);
      $this->update_setting($lesson_id, "mk_short_title", $lesson['short_title'] ?: $lesson['title']);
      $this->update_setting($lesson_id, "mk_short_desc", !isset($lesson['short_desc']) || empty($lesson['short_desc']) ? wp_trim_words($lesson['description'], 63, "...") : $lesson['short_desc']);
      $this->update_setting($lesson_id, "lesson_video_enabled", "on");
      $this->update_setting($lesson_id, "lesson_video_url", wp_upload_dir()['baseurl'] . "/katatonia.mp4");

      if (isset($lesson['duration'])) {
        $this->update_setting($lesson_id, "mk_duration", absint($lesson['duration'][2]) + (absint($lesson['duration'][1]) * 60) + (absint($lesson['duration'][0]) * 60 * 60));
      }

      if (isset($lesson['is_sample'])) {
        $this->update_setting($lesson_id, "sample_lesson", $lesson['is_sample'] ? "on" : null);
      }

      if (isset($lesson['poster'])) {
        $poster_id = $this->save_img($lesson['poster'], $lesson_id, $lesson['title']);
        $this->update_setting($lesson_id, "mk_lesson_poster", $poster_id);
      }
    }
  }

  private function courses() {
    foreach ($this->courses_data as $course_data) {
      $this->delog($course_data, "courses", "var_export");
      $slug = $course_data['slug'] ?: sanitize_title($course_data['title']);
      $this->delog($slug, "courses");

      try {
        Course::get_course_by_slug($slug);
        $this->delog(["Error: Course exists.", $slug], "courses");
        $this->stats['failed_courses']++;
        continue;
      } catch (\Throwable $th) {
        // course doesnt exists, we can continue
      }

      $course_id = wp_insert_post([
        "post_type" => learndash_get_post_type_slug('course'),
        "post_title" => $course_data['title'],
        "post_author" => 1,
        "post_name" => $slug,
        "post_content" => $course_data['description'],
        "post_status" => 'publish'
      ]);

      if (!$course_id || is_wp_error($course_id)) {
        $this->delog(["Error: course_id is_wp_error", $course_id], "courses");
        $this->stats['failed_courses']++;
        continue;
      }

      $this->stats['created_courses']++;

      if (isset($course_data['category']) && term_exists($course_data['category'], 'ld_course_category')) {
        $category_term = get_term_by("slug", $course_data['category'], "ld_course_category");
        if ($category_term && !is_wp_error($category_term)) {
          wp_set_post_terms($course_id, [$category_term->term_id], 'ld_course_category');
        } else {
          $this->delog(["Error: category_term is_wp_error", $category_term], "courses");
        }
      } else {
        $this->delog(["Course category missing", $course_data['category'] ?? null, $course_data['category'] ?? term_exists($course_data['category'], 'ld_course_category')], "courses");
      }

      $this->update_setting($course_id, "course_price_type", "closed");
      $this->update_setting($course_id, "course_disable_lesson_progression", "on");

      $short_title = $course_data['short_title'] ?: $course_data['title'];
      $short_desc = !isset($course_data['short_desc']) || empty($course_data['short_desc']) ? wp_trim_words($course_data['description'], 63, "...") : $course_data['short_desc'];
      $this->update_setting($course_id, "mk_short_title", $course_data['short_title'] ?: $course_data['title']);
      $this->update_setting($course_id, "mk_short_desc", $short_desc);
      $this->update_setting($course_id, "mk_master_name_fa", $course_data['master_name_fa'] ?: null);
      $this->update_setting($course_id, "mk_master_name_en", $course_data['master_name_en'] ?: null);
      $this->update_setting($course_id, "mk_trailer_url", wp_upload_dir()['baseurl'] . "/katatonia.mp4");

      if (isset($course_data['duration'])) {
        $this->update_setting($course_id, "mk_duration", absint($course_data['duration'][2]) + (absint($course_data['duration'][1]) * 60) + (absint($course_data['duration'][0]) * 60 * 60));
      }

      if (isset($course_data['book'])) {
        $this->update_setting($course_id, "mk_book_title", $course_data['book']['title'] ?: "کتاب کار " . $course_data['title']);
        $this->update_setting($course_id, "mk_book_desc", $course_data['book']['description'] ?: null);
        $this->update_setting($course_id, "mk_book_pages", $course_data['book']['pages'] ?: 0);

        if (isset($course_data['book']['poster'])) {
          $poster_id = $this->save_img($course_data['book']['poster'], null, $short_title);
          $this->update_setting($course_id, "mk_book_img", $poster_id);
        }
      }

      $course_image_id = null;
      if (isset($course_data['images']['profile'])) {
        $course_image_id = $this->save_img($course_data['images']['profile'], $course_id, $short_title);
        $this->update_setting($course_id, "mk_profile_img", $course_image_id);
      }

      if (isset($course_data['images']['portrait'])) {
        $portrait_id = $this->save_img($course_data['images']['portrait'], null, $short_title);
        $this->update_setting($course_id, "mk_portrait_img", $portrait_id);
      }

      if (isset($course_data['images']['typography'])) {
        $typography_id = $this->save_img($course_data['images']['typography'], null, $short_title);
        $this->update_setting($course_id, "mk_typography_img", $typography_id);
      }

      if (isset($course_data['images']['cinematic'])) {
        $cinematic_id = $this->save_img($course_data['images']['cinematic'], null, $short_title);
        $this->update_setting($course_id, "mk_cinematic_img", $cinematic_id);
      }

      if (isset($course_data['images']['landscape'])) {
        $landscape_id = $this->save_img($course_data['images']['landscape'], null, $short_title);
        $this->update_setting($course_id, "mk_landscape_img", $landscape_id);

        if (!isset($course_data['images']['trailer'])) {
          $this->update_setting($course_id, "mk_trailer_poster", $landscape_id);
        }
      }

      if (isset($course_data['images']['trailer'])) {
        $trailer_id = $this->save_img($course_data['images']['trailer'], null, $short_title);
        $this->update_setting($course_id, "mk_trailer_poster", $trailer_id);
      }

      if (isset($course_data['lessons'])) {
        $this->lessons($course_data['lessons'], $course_id);
      }
    }
  }

  private function save_img($url, $post_id = null, $desc = null) {
    $url = str_starts_with($url, "http") ? $url : "https://masterkelas.com/wp-content/uploads/$url";
    $id = media_sideload_image($url, $post_id, $desc, 'id');
    if (!is_wp_error($id)) {
      $this->stats['downloaded_images']++;
      if ($post_id > 0) {
        set_post_thumbnail($post_id, $id);
      }
    } else {
      $this->delog(["Error: id is_wp_error", $id, $url, $post_id, $desc], "images");
      $this->stats['failed_images']++;
    }

    return $id;
  }

  private function update_setting($id, $key, $value) {
    if (is_wp_error($value)) {
      $this->delog(["Error: value is_wp_error", $id, $key, $value], "settings");
      return;
    }

    learndash_update_setting($id, $key, $value);
  }

  private function delog($data, $group = false, $output = 'json') {
    switch ($output) {
      case 'json':
        if (!is_string($data)) {
          $data = $this->raw_json_encode($data);
        }
        break;
      case 'var_export':
        $data = var_export($data, true);
        break;
    }

    // \WP_CLI::debug($json && !is_string($data) ? $this->raw_json_encode($data) : $data, "masterkelas");
    \WP_CLI::debug($data, $output === 'var_export' ? "mkfull" : "mk");
  }

  private function raw_json_encode($input, $flags = 0) {
    $fails = implode('|', array_filter(array(
      '\\\\',
      $flags & JSON_HEX_TAG ? 'u003[CE]' : '',
      $flags & JSON_HEX_AMP ? 'u0026' : '',
      $flags & JSON_HEX_APOS ? 'u0027' : '',
      $flags & JSON_HEX_QUOT ? 'u0022' : '',
    )));
    $pattern = "/\\\\(?:(?:$fails)(*SKIP)(*FAIL)|u([0-9a-fA-F]{4}))/";
    $callback = function ($m) {
      return html_entity_decode("&#x$m[1];", ENT_QUOTES, 'UTF-8');
    };
    return preg_replace_callback($pattern, $callback, json_encode($input, $flags));
  }

  private $categories_data = [
    [
      "title" => "آشپزی",
      "slug" => "culinary-arts"
    ],
    [
      "title" => "طراحی و استایل",
      "slug" => "design-photography-fashion"
    ],
    [
      "title" => "هنر و سرگرمی",
      "slug" => "film-tv"
    ],
    [
      "title" => "موسیقی",
      "slug" => "music-entertainment"
    ],
    [
      "title" => "کسب و کار",
      "slug" => "business-politics-society"
    ],
    [
      "title" => "ورزش و بازی",
      "slug" => "sports-games"
    ],
    [
      "title" => "نوشتن",
      "slug" => "writing"
    ],
    [
      "title" => "علم و فناوری",
      "slug" => "science-technology"
    ],
    [
      "title" => "خانه و سبک زندگی",
      "slug" => "lifestyle"
    ],
    [
      "title" => "جامعه و دولت",
      "slug" => "community-government"
    ],
    [
      "title" => "سلامتی",
      "slug" => "wellness"
    ],
  ];

  private $courses_data = [
    [
      "title" => "آموزش کارآفرینی خلاق",
      "slug" => "richard-branson-teaches-disruptive-entrepreneurship",
      "category" => "business-politics-society",
      "short_desc" => "ریچارد برانسون کارآفرین نمونه به شما نحوه تبدیل عجیب ترین رویاهاتون به کارهای موفقیت آمیز را در حالی که دارید خوشگذرانی می کنید آموزش می دهد.",
      "master_name_fa" => "ریچارد برانسون",
      "master_name_en" => "Richard Branson",
      "duration" => ["2", "24", "00"],
      "description" => "گاهی اوقات، کار بزرگ کردن یعنی خوش گذرونی. ریچارد برانسون، موسس گروه ویرجین به وسیله ی حل مشکلاتی که برای خودش جالب بودند، امپراطوری خفنی برای خودش ساخت، و به سمت هر صنعتی که رفته اون ها رو متحول کرده و رویاهایی رو دنبال کرده که بنظر غیر ممکن بودند. این ماجراجویی اون رو از شروع های تازه به سمت ستاره‌ها برده. شما هم یاد بگیرید که چجوری میتوانید، ایده‌هایی یاد بگیرید که از خوبی زیاد ترسناک هستند، به سمت ترس هاتون برید و به جایگاه های بالا برسید.",
      "book" => [
        "pages" => 52,
        "poster" => "Richard-Branson-Workbook.jpg",
        "description" => "یک کتاب کار راهنما که دنیای دیوانه کننده ی ریچارد برانسون در آن وجود دارد، همراه با نصیحت هایی راجع به ریسک پذیری سالم، ساختن تیم عالی و کمک کردن به آدم های دیگر."
      ],
      "images" => [
        "profile" => "Richard-Branson-Profile.jpg",
        "cinematic" => "Richard-Branson-Cinematic.jpg",
        "portrait" => "Richard-Branson-Portrait.jpg",
        "landscape" => "Richard-Branson-Thriller.jpg",
        "typography" => "Richard-Branson-Logotype.png",
      ],
      "lessons" => [
        [
          "title" => "با ریچارد برانسون بیشتر آشنا شوید",
          "duration" => ["00", "05", "56"],
          "is_sample" => true,
          "description" => "ریچارد در این قسمت از مسترکلاس دیدگاه و نگرش خودش را که برای گرفتن خیلی از تصمیمات کاری و زندگیش استفاده کرده برای شما بازگو میکنه و در ادامه یک مثال از تفکرات بی پروای خودش میزنه و از مواردی که دانشجویان انتظار دارند در این کلاس آموزش ببینند صحبت میکنه."
        ],
        [
          "title" => "با اولین معامله‌ آتش روشن کنید",
          "duration" => ["00", "17", "01"],
          "is_sample" => true,
          "description" => "ریچارد برانسون در پانزده سالگی، اولین معامله‌ موفقیت آمیز خودش را انجام داد. یک مجله دانش آموزی، در یک مدرسه شبانه روزی، جایی که آموزش سنتی بریتانیایی با علاقه ریچارد به جهان در تعارض کامل بود. نحوه بررسی علایق ریچارد توسط خودش و استفاده از آن ها برای تصمیم گرفتن در کارهای خودش را از او بیاموزید."
        ],
        [
          "title" => "ورود بی پروا به صنعت موسیقی",
          "duration" => ["00", "12", "49"],
          "is_sample" => true,
          "description" => "فلسفه‌ی ریچارد درباره‌ی زندگی باعث شد وی تصمیماتی با ریسک بسیار بالا در ابتدای مسیر خودش بگیرد. با تصمیمش درباره‌ی ساخت یک گروه و بند موسیقی این قضیه شروع شد و جایگاه خودش را با خوانندگی در یک گروه موسیقی خیلی نامتعارف، سکس پستولز، خاص کرد."
        ],
        [
          "title" => "با ناامیدی های خود مقابله کنید: بهبود تجارت خطوط هوایی",
          "duration" => ["00", "08", "41"],
          "is_sample" => false,
          "description" => "ریچارد توضیح می دهد که چگونه مشکلاتش با خدمات خط هوایی جرقه ای برای استارت خطوط هوایی ویرجین آتلانتیک شد و او با چه سرعت برق آسایی این رویا رو به واقعیت تبدیل کرد. اون همچنین استراتژی هایی برای سازماندهی ایده‌ها و تبدیل اون ها به لیست هایی که قابل مدیریت هستند و انگیزه ای برای ایجاد اختراع میشوند را برای شما بازگو می کند.",
          "poster" => "Richard-Branson-LT-4.jpg",
        ],
        [
          "title" => "چگونه در برابر صنعتی که مثل غول جلیات هست، حضرت داوود باشیم",
          "duration" => ["00", "12", "54"],
          "is_sample" => false,
          "description" => "زمانی که شما به یک صنعت بزرگ ورود پیدا می کنید، قطعا مشکلاتی ایجاد می شود. ریچارد برانسون در مورد راهنمایی و توصیه افراد پیشکسوتی که او را در برابر خط های هوایی که بسیار قوی بودند تا در مقابل آن ها دوام بیاورد حرف میزند.",
          "poster" => "Richard-Branson-LT-5.jpg",
        ],
        [
          "title" => "با چنگ و دندان بجنگید تا مشکلات را حل کنید",
          "duration" => ["00", "20", "47"],
          "is_sample" => false,
          "description" => "در حین این ماجراجویی‌ها، ریچارد خودش رو از چندین مخمصه نجات داده است. در این قسمت از کلاس، او به شما نحوه پیدا کردن راه درست را در انبوهی از مشکلات که پیش آمده است می گوید. از قبول کردن حمایت دولت تا گرفتن تصمیم‌های سخت در بالن و در طوفان.",
          "poster" => "Richard-Branson-LT-6.jpg",
        ],
        [
          "title" => "پیدا کردن افراد معروف و بزرگ",
          "duration" => ["00", "10", "58"],
          "is_sample" => false,
          "description" => "ریچارد برانسون کارهایی که برای پیدا کردن رهبران عالی لازم بود انجام بده رو توضیح می دهد. وی استراتژی های خودش را برای سنجش شخصیت ها با شما به اشتراک میگذارد، یخ آدم ها را با یک ویدیوی عالی باز کنید، و رزومه دادن رو برای همیشه کنار بگذارید.",
          "poster" => "Richard-Branson-LT-7.jpg",
        ],
        [
          "title" => "تیمتون رو ارزشمند جلوه بدید",
          "duration" => ["00", "12", "13"],
          "is_sample" => false,
          "description" => "شما تیم خودتون رو تشکیل دادید، ولی چجوری بازدهی تیم رو بالا ببریم؟ ریچارد مثال هایی از فیدبک های سازنده میزنه، راه هایی که میتونید کاری کنید تیمتون باهوش و چالاک بمونه.",
          "poster" => "Richard-Branson-LT-8.jpg",
        ],
        [
          "title" => "گوش دادن دقیق به دیگران و انجام دادن کارها",
          "duration" => ["00", "10", "01"],
          "is_sample" => false,
          "description" => "همه چی از یک دفترچه شروع میشه. ریچارد بهتون میگه که گوش دادن دقیق به دیگران چطور هست و یه نگاه خاص به دفترچه هاش میندازه و آگاه باشید نکات بسیار مهمی در مورد اینکه او چگونه کارهاش رو برنامه ریزی میکنه تا به این حد از موفقیت برسه در اون دفترچه موجود هست.",
          "poster" => "Richard-Branson-LT-9.jpg",
        ],
      ]
    ],
    [
      "title" => "آموزش میکاپ و آرایش",
      "slug" => "create-makeup-looks-for-any-moment",
      "category" => "design-photography-fashion",
      "short_desc" => "با سِر جان، میکاپ آرتیست معروف در طی 30 روز مهارت های آرایشی خود را ارتقا دهید. روتین مراقب پوستی خود را بسازید، جعبه ی لوازم آرایشی خود را درست کنید و سه مورد از بهترین آرایش های سر جان را بیاموزید: مدل بی آرایش، مدل هر روز و مدل رویایی.",
      "master_name_fa" => "سر جان",
      "master_name_en" => "Sir John",
      "duration" => ["2", "54", "00"],
      "description" => "سِر جان که آرایش های رویایی ستاره هایی مثل بیانسه، خوان اسمالز، زِندیا و باربی هست یکی از محبوب ترین هنرمندانی هست که آرایش کردن افراد روی فرش قرمز به عهده ی اون بوده. تاثیر جهانی او روی فرهنگ زیبایی جهانی تاثیر گذار بوده و او به این هنر چشم انداز کاملی دارد.",
      "book" => [
        "pages" => 23,
        "poster" => "Sir-John-Workbook.jpg",
        "description" => "کتاب کار راهنمای قابل دانلود پر از نکات و موارد مهم دیگر مربوط به این کلاس."
      ],
      "images" => [
        "profile" => "Sir-John-Profile.jpg",
        "cinematic" => "Sir-John-Cinematic.jpg",
        "portrait" => "Sir-John-Portrait.jpg",
        "landscape" => "Sir-John-Thriller.jpg",
        "typography" => "Sir-John-Logotype.png",
      ],
      "lessons" => [
        [
          "title" => "بررسی اجمالی این کلاس",
          "duration" => ["00", "02", "39"],
          "is_sample" => true,
          "description" => "سِر جان همه ی موارد آموزشی که طی 30 روز آینده قرار است یاد بگیرید به شما معرفی می کند. آماده شوید تا چیزهایی که باعث زیبایی شما می شوند را هایلایت کنید."
        ],
        [
          "title" => "با استاد میکاپ، سِر جان آسنا شوید",
          "duration" => ["00", "03", "07"],
          "is_sample" => true,
          "description" => "سِر جان، بچه ای که دانشجوی هنر بوده و حالا به یک میکاپ آرتیست معروف جهانی تبدیل شده، داستان الهام بخش خودش برای موفق شدن را برای شما بازگو می کند."
        ],
        [
          "title" => "درباره‌ افکارتان در مورد زیبایی فکر کنید",
          "duration" => ["00", "01", "43"],
          "is_sample" => true,
          "description" => "سِر جان ازلوازم آرایشی برای پوشاندن نقص‌ها استفاده نمیکند، او از لوازم آرایشی برای پررنگ کردن و جشن گرفتن استفاده میکند. یاد بگیرید چگونه چیزهایی را که از درون و بیرون باعث زیبایی شما میشوند، شناسایی کنید."
        ],
        [
          "title" => "انواع پوست",
          "duration" => ["00", "01", "41"],
          "is_sample" => false,
          "description" => "نوع پوست خودتان را بشناسید و یاد بگیرید که چگونه پوست خود را شناسایی کنید تا بتوانید روتین پوستی مناسب برای خودتان داشته باشید.",
          "poster" => "Sir-John-LT-4.jpg",
        ],
        [
          "title" => "ساختن روتین پوستی",
          "duration" => ["00", "15", "37"],
          "is_sample" => false,
          "description" => "انواع مختلفی از محصولات پوستی وجود دارند، از کجا باید بفهمیم چه چیزی برای ما مناسب است؟ سِر جان تک تک محصولات موجود در بازار را توضیح می دهد، از کارایی اون ها و برای چه مدل پوست هایی مناسب هستند با شما صحبت می کند، تا شما بتوانید یک روتین پوستی مناسب برای خودتان بسازید.",
          "poster" => "Sir-John-LT-5.jpg",
        ],
        [
          "title" => "ابزارهای مراقبت از پوست مورد علاقه سِر جان",
          "duration" => ["00", "03", "34"],
          "is_sample" => false,
          "description" => "سر جان ابزارهای حرفه‌ای آرایش خودش و نحوه‌ی استفاده از آنها را با شما به اشتراک میگذارد.",
          "poster" => "Sir-John-LT-6.jpg",
        ],
        [
          "title" => "بِراش‌ها",
          "duration" => ["00", "14", "52"],
          "is_sample" => false,
          "description" => "سِر جان بِراش هایی را که یک آدم حرفه‌ای از آن ها برای چشم ها، گونه، زاویه سازی، برنز کردن و پایه ریزی استفاده می کنند توضیح می دهد.",
          "poster" => "Sir-John-LT-7.jpg",
        ],
        [
          "title" => "تکمیل کردن جعبه‌ی لوازم آرایش خودتان",
          "duration" => ["00", "07", "40"],
          "is_sample" => false,
          "description" => "درباره‌ی همه ی ابزاری که در جعبه ی لوازم آرایش وجود دارند که شامل یک سری لوازم ضروری سِر جان هستند، نکاتی را بیاموزید.",
          "poster" => "Sir-John-LT-8.jpg",
        ],
        [
          "title" => "وسایل ضروری جعبه‌ی لوازم آرایش",
          "duration" => ["00", "21", "51"],
          "is_sample" => false,
          "description" => "سِر جان یک سری از محصولات آرایشی مورد علاقه ی خودش را با شما به اشتراک میگذارد و نکاتی را درباره ی درست کردن جعبه ی لوازم آرایشی با بودجه ی مناسب به شما می دهد.",
          "poster" => "Sir-John-LT-9.jpg",
        ],
        [
          "title" => "آماده سازی پوست",
          "duration" => ["00", "04", "45"],
          "is_sample" => false,
          "description" => "سِر جان را دنبال کنید تا پوست خود را برای لوازم آرایشی آماده کنید.",
          "poster" => "Sir-John-LT-10.jpg",
        ],
      ]
    ],
    [
      "title" => "آموزش ساخت ویدیوی جذاب پر سر و صدا",
      "slug" => "make-compelling-videos-that-go-viral",
      "category" => "film-tv",
      "short_desc" => "سرگرم کننده و در دسترس، بررسی های اخیر MKBHD درباره آخرین و بهترین تکنولوژی ها باعث شده که بهترین خالق این دهه باشد. او بیشتر از 15 میلیون دنبال کننده دارد، و یه سری مصاحبه های خفن دارد برای مثال ایلان ماسک و رئیس جمهور سابق باراک اوباما.",
      "master_name_fa" => "مارکوس برانلی",
      "master_name_en" => "Marques Brownlee",
      "duration" => ["1", "23", "00"],
      "description" => "در عرض سی روز کیفیت محتواتون رو بالا ببرید. ویدیوی بعدیتون رو در کنار MKBHD افسانه ای درست کنید در حالیکه اون داره بهتون یاد میده چجوری محتوای جذاب توی هر پلتفرمی درست کنید. از ایده‌پردازی تا فیلمبرداری، نورپزداری و حتی اجرای جلوی دوربین، جلسات MKBHD بهتون انگیزه میده تا محتوای جذابی برای مخاطبینتون بسازید و شما اینکار را درعرض 30 روز انجام خواهید داد.",
      "book" => [
        "pages" => 30,
        "poster" => "Marques-Brownlee-Workbook.jpg",
        "description" => ""
      ],
      "images" => [
        "profile" => "Marques-Brownlee-Profile.jpg",
        "cinematic" => "Marques-Brownlee-Cinematic.png",
        "portrait" => "Marques-Brownlee-Portrait.png",
        "landscape" => "Marques-Brownlee-Thriller.jpg",
        "typography" => "Marques-Brownlee-Logotype.png",
      ],
      "lessons" => [
        [
          "title" => "نگاه کلی به جلسات",
          "duration" => ["00", "04", "20"],
          "is_sample" => true,
          "description" => "مارکوس برانلی،که به اسم MKBHD شناخته میشه، بهتون میگه که واقعا کیه، کارش چیه، و اینکه توی سی روز آینده چی یاد میگیرید."
        ],
        [
          "title" => "با مربیتون، MKBHD، آشنا بشید",
          "duration" => ["00", "05", "17"],
          "is_sample" => true,
          "description" => "مارکوس شروع متواضعانه ی حرفه‌اش رو باهامون به اشتراک میزاره و اینکه چجوری در نهایت تونست سرگرمیش رو تبدیل به یه شغل درآمدزا بکنه."
        ],
        [
          "title" => "انتخاب موضوع",
          "duration" => ["00", "06", "25"],
          "is_sample" => true,
          "description" => "بعد از اینکه مثالهایی درباره کار خودش میزنه و توضیح میده که چجوری موضوعات رو انتخاب میکنه،مارکوس روشی رو که برای ساخت مفاهیم پیشنهاد میکنه رو بهتون میگه.بعدش شما میشینید و برای ویدیوی بعدیتون ایده میدید."
        ],
        [
          "title" => "چک لیست چهارچوب کاریتون",
          "duration" => ["00", "04", "01"],
          "is_sample" => false,
          "description" => "مارکوس چک لیست چهارچوب کاری رو معرفی میکنه،که به عنوان یه مرجع تو کل کلاس استفاده میشه. این چک لیست شامل هر چیزیکه شما باید در حین اینکه دارید به محتوای ویدیوتون فکر میکنید و واسش برنامه میریزید،میشه.از ایده دادن گرفته تا طراحی کردن.",
          "poster" => "Marques-Brownlee-LT-4.jpg",
        ],
        [
          "title" => "نگاه کلی به دوربین",
          "duration" => ["00", "02", "06"],
          "is_sample" => false,
          "description" => "مارکوس سه نوع دوربین رو که قراره توی این قسمت ازشون استفاده کنه معرفی میکنه: یه گوشی هوشمند،یه دوربین بی آیینه و یه دوربین سینمایی.",
          "poster" => "Marques-Brownlee-LT-5.jpg",
        ],
        [
          "title" => "دوربین با توجه به هر بودجه‌ای",
          "duration" => ["00", "12", "54"],
          "is_sample" => false,
          "description" => "حالا که انواع مختلف دوربین‌هارو شناختید،مارکوس بهتون کمک میکنه با توجه به بودجه‌تون یکی رو انتخاب کنید.اون جزئیات تکنیکی و خوبی های هر طراحی رو بهتون میگه.",
          "poster" => "Marques-Brownlee-LT-6.jpg",
        ],
        [
          "title" => "لنز",
          "duration" => ["00", "21", "37"],
          "is_sample" => false,
          "description" => "بیاید راجع به لنزها حرف بزنیم؛یعنی همون تیکه شیشه ی جادویی که بهتون کمک میکنه اون حس و تصویری که میخواید رو برای ویدیوتون درست کنید.مارکوس راجع به قطر و عمقشون حرف میزنه،همچنین راجع به زوم کردن،لنزهای تله فوتو و پرایم هم حرف میزنه.",
          "poster" => "Marques-Brownlee-LT-7.jpg",
        ],
        [
          "title" => "نورپردازی",
          "duration" => ["00", "20", "15"],
          "is_sample" => false,
          "description" => "مارکوس از اهمیت نورپردازی حرف میزنه و شمارو به فکر میندازه که چجوری شمایل و حس یه صحنه روی مخاطبتون تاثیر میزاره.",
          "poster" => "Marques-Brownlee-LT-8.jpg",
        ],

      ]
    ],
  ];
}
\WP_CLI::add_command('master-factory', '\MasterKelas\Command\MasterFactory');
