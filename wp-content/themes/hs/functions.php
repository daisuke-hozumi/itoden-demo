<?php
/**
 * hs functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package hs
 */

if ( ! function_exists( 'hs_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function hs_setup() {
		global $rt;
		$rt = get_template_directory_uri();
		global $rtphp;
		$rtphp = get_template_directory();
		
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on hs, use a find and replace
		 * to change 'hs' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'hs', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
        add_image_size('yarpp-thumbnail', "318", "201", true);
        
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'hs' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'hs_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'hs_setup' );

// 人気記事出力用
function getPostViews($postID){
	$count_key = 'post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if($count==''){
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
			return "0 View";
	}
	return $count.' Views';
}

function setPostViews($postID) {
	$count_key = 'post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if($count==''){
			$count = 0;
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
	}else{
			$count++;
			update_post_meta($postID, $count_key, $count);
	}
}

//------------------
// デフォルトの投稿を削除
//------------------
add_action( 'admin_menu', 'remove_menus' );
function remove_menus(){
    remove_menu_page( 'edit.php' ); //投稿メニュー
    remove_menu_page( 'edit-comments.php' ); //コメントメニュー
}

/*
 メニューカスタム
*/
function custom_menu_order($menu_ord) {
    if(!$menu_ord) return true;

    return array(
        'index.php', // ダッシュボード
        'separator1', // 最初の区切り線
        'edit.php?post_type=blog', // todo:ブログ
        'edit.php?post_type=news', 
        'separator2',
    );
}
add_filter('custom_menu_order', 'custom_menu_order'); // Activate custom_menu_order
add_filter('menu_order', 'custom_menu_order');

// コア機能の無駄実装を削除
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

// ContactForm7 完了画面遷移
add_action( 'wp_footer', 'add_thanks_page' );
function add_thanks_page() {
echo <<< EOD
<script>
document.addEventListener( 'wpcf7mailsent', function( event ) {
EOD;
echo("location = '" . (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"]) . "/contact-thanks/';";
echo <<< EOD2
}, false );
</script>
EOD2;
}

// ビジュアルエディター用CSSファイル使用を宣言
add_editor_style();
// ビジュアルエディター用CSSファイル読み込み
add_editor_style("editor-style.css");

remove_filter('the_content', 'wpautop');// 記事の自動整形を無効にする
remove_filter('the_excerpt', 'wpautop');// 抜粋の自動整形を無効にする

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function hs_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'hs_content_width', 640 );
}
add_action( 'after_setup_theme', 'hs_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function hs_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'hs' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'hs' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'hs_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function hs_scripts() {
//	wp_enqueue_style( 'hs-style', get_stylesheet_uri() );

	// wp_enqueue_script( 'hs-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	// wp_enqueue_script( 'hs-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		// wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'hs_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/* 絵文字の読み込みを削除 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles', 10 );
//show_admin_bar( false );


/**
 * カスタム投稿タイプの追加
 * 
 * 他パラメーターは、以下より確認できます。
 *  http://wpdocs.m.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/register_post_type
 */
add_action( 'init', 'create_post_type' );
function create_post_type() {
    register_post_type( 'news', [ // 投稿タイプ名の定義
        'labels' => [
            'name'          => '新着情報', // 管理画面上で表示する投稿タイプ名
            'singular_name' => 'news',    // カスタム投稿の識別名
        ],
        'public'        => true,  // 投稿タイプをpublicにするか
        'has_archive'   => false, // アーカイブ機能ON/OFF
        'menu_position' => 5,     // 管理画面上での配置場所
        'show_in_rest'  => true,  // 5系から出てきた新エディタ「Gutenberg」を有効にする
		'yarpp_support' => true,
        'supports'      => array( 
							'title', //タイトル
							'editor', //内容の編集
							'author', //作成者
							'thumbnail', //アイキャッチ画像
							'excerpt', //抜粋
							'comments', //コメント機能の他、編集画面にコメント数のバルーンを表示する
							'revisions' //リビジョンを保存する
						) //カスタム投稿タイプに実装させる機能
    ]);
	register_post_type( 'blog', [ // 投稿タイプ名の定義
        'labels' => [
            'name'          => 'ブログ', // 管理画面上で表示する投稿タイプ名
            'singular_name' => 'blog',    // カスタム投稿の識別名
        ],
        'public'        => true,  // 投稿タイプをpublicにするか
        'has_archive'   => true, // アーカイブ機能ON/OFF
        'menu_position' => 5,     // 管理画面上での配置場所
        'show_in_rest'  => true,  // 5系から出てきた新エディタ「Gutenberg」を有効にする
		'yarpp_support' => true,
        'supports'      => array( 
							'title', //タイトル
							'editor', //内容の編集
							'thumbnail',
							'revisions' //リビジョンを保存する
						) //カスタム投稿タイプに実装させる機能
    ]);
	
	// ※カスタム投稿タイプでＷＰコアのカテゴリーを使用する場合のみ有効化
	register_taxonomy_for_object_type('category', 'blog');
};


// カスタム投稿でaddquicktagを使用
function addquicktag_post_types($post_types) {
    $post_types[] = 'news';
	$post_types[] = 'blog';
    return $post_types;
}
add_filter('addquicktag_post_types', 'addquicktag_post_types');

/**
 * カスタムタクソノミーの追加
 * 
 * 参照 :https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/register_taxonomy
 */
function add_taxonomy() {
	//register_taxonomy(
	//	'movie_tag', //（必須） タクソノミーのスラッグ。英小文字とアンダースコアのみ、32文字以下
	//	'movie', //（必須） タクソノミーを適用させる投稿タイプ。 WordPress 標準の投稿タイプ、または登録されているカスタム投稿タイプを選択できる。複数指定する場合は配列で指定。
	//	array(
	//		'labels' => array(
	//		'name' => 'タグ'
	//	),
	//'hierarchical' => false //タクソノミーが階層構造(親子関係)を持つか
	//));
}
add_action( 'init', 'add_taxonomy' );


/**
 * 画像サイズの追加
 */
add_action( 'after_setup_theme', 'baw_theme_setup' ); // 参照 https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/add_action
function baw_theme_setup() {
 add_image_size( // https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/add_image_size
		'archive-thumbnail', //(必須) 新しい画像サイズの名前
		400, //幅(px)
		300, //高さ(px)
		true //トリミングをするか否か(元画像の幅か高さが、前述の指定した幅か高さより小さい場合は、トリミングはされません)
	 //注意；一度生成された画像のサイズは、 add_image_size()で大きさを変えても変わりません。
	 //画像のリサイズには、プラグイン「regenerate thumbnails」が必要になります。
	);
    add_image_size( 'yarpp-thumbnail', 318, 210, true );
}
    
/*---------------------------
 無料相談件数を取得
---------------------------*/
function funcGetSoudanCount() {
    echo(get_field('free_count',701));
}

/**
 * パンくずリスト
 */
if ( ! function_exists( 'custom_breadcrumb' ) ) {
    function custom_breadcrumb( $wp_obj = null ) {

        // トップページでは何も出力しない
        if ( is_home() || is_front_page() ) return false;

        //そのページのWPオブジェクトを取得
        $wp_obj = $wp_obj ?: get_queried_object();

        echo '<div id="breadcrumb">'.  //id名などは任意で
                '<ul itemscope="" itemtype="http://schema.org/BreadcrumbList" class="cont cl">'.
                    '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">'.
                        '<a href="'. home_url() .'" itemprop="item" class="home"><span itemprop="name">HOME</span></a> <meta itemprop="position" content="1">'.
                    '</li>';

        if ( is_attachment() ) {

            /**
             * 添付ファイルページ ( $wp_obj : WP_Post )
             * ※ 添付ファイルページでは is_single() も true になるので先に分岐
             */
            echo '<li><span>'. $wp_obj->post_title .'</span></li>';

        } elseif ( is_single() ) {

            /**
             * 投稿ページ ( $wp_obj : WP_Post )
             */
            $post_id    = $wp_obj->ID;
            $post_type  = $wp_obj->post_type;
            $post_title = $wp_obj->post_title;
            $parent_num="2";
            
            // カスタム投稿タイプかどうか
			// todo:パンくず不要な「アーカイブ無しページ」は以下のコメントアウトのように除外する
			// if ( $post_type !== 'post' && $post_type !== 'news' ) {
            if ( $post_type !== 'post' ) {

                $the_tax = "";  //そのサイトに合わせ、投稿タイプごとに分岐させて明示的に指定してもよい

                // 投稿タイプに紐づいたタクソノミーを取得 (投稿フォーマットは除く)
                $tax_array = get_object_taxonomies( $post_type, 'names');
                foreach ($tax_array as $tax_name) {
                    if ( $tax_name !== 'post_format' ) {
                        $the_tax = $tax_name;
                        break;
                    }
                }

                //カスタム投稿タイプ名の表示
                echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">'.
                        '<a href="'. get_post_type_archive_link( $post_type ) .'"  itemprop="item">'.
                            '<span itemprop="name">'. get_post_type_object( $post_type )->label .'</span>'.
                        '</a><meta itemprop="position" content="' . $parent_num . '"></li>';
                $parent_num++;
            } else {
                $the_tax = 'category';  //通常の投稿の場合、カテゴリーを表示
            }
			
            // タクソノミーが紐づいていれば表示
            if ( $the_tax !== "" ) {

                $child_terms = array();   // 子を持たないタームだけを集める配列
                $parents_list = array();  // 子を持つタームだけを集める配列

                // 投稿に紐づくタームを全て取得
                $terms = get_the_terms( $post_id, $the_tax );

                if ( !empty( $terms ) ) {

                    //全タームの親IDを取得
                    foreach ( $terms as $term ) {
                        if ( $term->parent !== 0 ) $parents_list[] = $term->parent;
                    }

                    //親リストに含まれないタームのみ取得
                    foreach ( $terms as $term ) {
                        if ( ! in_array( $term->term_id, $parents_list ) ) $child_terms[] = $term;
                    }

                    // 最下層のターム配列から一つだけ取得
                    $term = $child_terms[0];

                    if ( $term->parent !== 0 ) {

                        // 親タームのIDリストを取得
                        $parent_array = array_reverse( get_ancestors( $term->term_id, $the_tax ) );
                        foreach ( $parent_array as $parent_id ) {
                            $parent_term = get_term( $parent_id, $the_tax );
                            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">'.
                                    '<a href="'. get_term_link( $parent_id, $the_tax ) .'" itemprop="item">'.
                                      '<span itemprop="name">'. $parent_term->name .'</span>'.
                                    '</a><meta itemprop="position" content="' . $parent_num . '">'.
                                 '</li>';
                            $parent_num++;
                        }
                    }

                    // 最下層のタームを表示
                    echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">'.
                            '<a itemprop="item" href="'. get_term_link( $term->term_id, $the_tax ). '">'.
                              '<span itemprop="name">'. $term->name .'</span>'.
                            '</a><meta itemprop="position" content="' . $parent_num . '">'.
                         '</li>';
                    $parent_num++;
                }
            }

            // 投稿自身の表示
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">'. $post_title .'</span><meta itemprop="position" content="' . $parent_num . '"></li>';

        } elseif ( is_page() ) {

            /**
             * 固定ページ ( $wp_obj : WP_Post )
             */
            $page_id    = $wp_obj->ID;
            $page_title = $wp_obj->post_title;

            $parent_num = 2;
            // 親ページがあれば順番に表示
            if ( $wp_obj->post_parent !== 0 && strpos($page_title,"お問い合わせ") === false ) {
                if ( $wp_obj->post_parent !== 0 ) {
                    $parent_array = array_reverse( get_post_ancestors( $page_id ) );
                    foreach( $parent_array as $parent_id ) {
                        echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">'.
                                '<a itemprop="item" href="'. get_permalink( $parent_id ).'">'.
                                    '<span itemprop="name">'.get_the_title( $parent_id ).'</span>'.
                                '</a><meta itemprop="position" content="' . $parent_num . '">'.
                             '</li>';
                        $parent_num++;
                    }
                }
            }
            // 投稿自身の表示
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">'. $page_title .'</span><meta itemprop="position" content="' . $parent_num . '"></li>';

        } elseif ( is_post_type_archive() ) {

            /**
             * 投稿タイプアーカイブページ ( $wp_obj : WP_Post_Type )
             */
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">'. $wp_obj->label .'</span><meta itemprop="position" content="2"></li>';

        } elseif ( is_date() ) {

            /**
             * 日付アーカイブ ( $wp_obj : null )
             */
            $year  = get_query_var('year');
            $month = get_query_var('monthnum');
            $day   = get_query_var('day');

            if ( $day !== 0 ) {
                //日別アーカイブ
                echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><a itemprop="item" href="'. get_year_link( $year ).'"><span itemprop="name">'. $year .'年</span></a><meta itemprop="position" content="2"></li>'.
                     '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><a itemprop="item" href="'. get_month_link( $year, $month ). '"><span itemprop="name">'. $month .'月</span></a><meta itemprop="position" content="3"></li>'.
                     '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">'. $day .'日</span><meta itemprop="position" content="4"></li>';

            } elseif ( $month !== 0 ) {
                //月別アーカイブ
                echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><a itemprop="item" href="'. get_year_link( $year ).'"><span itemprop="name">'.$year.'年</span></a><meta itemprop="position" content="2"></li>'.
                     '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">'.$month . '月</span><meta itemprop="position" content="3"></li>';

            } else {
                //年別アーカイブ
                echo '<li><span>'.$year.'年</span></li>';

            }

        } elseif ( is_author() ) {

            /**
             * 投稿者アーカイブ ( $wp_obj : WP_User )
             */
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">'. $wp_obj->display_name .' の執筆記事</span><meta itemprop="position" content="4"></li>';

        } elseif ( is_archive() ) {

            /**
             * タームアーカイブ ( $wp_obj : WP_Term )
             */
            $term_id   = $wp_obj->term_id;
            $term_name = $wp_obj->name;
            $tax_name  = $wp_obj->taxonomy;

            /* ここでタクソノミーに紐づくカスタム投稿タイプを出力しても良いでしょう。 */
			if ( is_category() ) {
				// ここに入ってきている、かつカテゴリーページの場合
				$post_type = get_post_type( $post );
			} else {
				$taxonomy = get_query_var('taxonomy');
				$post_type = get_taxonomy($taxonomy)->object_type[0];
			}
            
            if ( have_posts() ) {
                
            }
            //カテゴリーIDからURL文字列を取得
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><a itemprop="item" href="'. get_post_type_archive_link($post_type) . '"><span itemprop="name">' . get_post_type_object($post_type)->label . '</span></a><meta itemprop="position" content="2"></li>';
            
            $parent_num = 3;
            
            // 親ページがあれば順番に表示
            if ( $wp_obj->parent !== 0 ) {

                $parent_array = array_reverse( get_ancestors( $term_id, $tax_name ) );
                foreach( $parent_array as $parent_id ) {
                    $parent_term = get_term( $parent_id, $tax_name );
                    echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">'.
                            '<a itemprop="item" href="'. get_term_link( $parent_id, $tax_name ) .'">'.
                                '<span itemprop="name">'. $parent_term->name .'</span>'.
                            '</a><meta itemprop="position" content="' . $parent_num . '">'.
                         '</li>';
                    $parent_num++;
                }
            }

            // ターム自身の表示
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">'.
                    '<span itemprop="name">'. $term_name .'</span><meta itemprop="position" content="' . $parent_num . '">'.
                '</li>';


        } elseif ( is_search() ) {

            /**
             * 検索結果ページ
             */
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">「'. get_search_query() .'」で検索した結果</span><meta itemprop="position" content="2"></li>';

        
        } elseif ( is_404() ) {

            /**
             * 404ページ
             */
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">お探しの記事は見つかりませんでした。</span><meta itemprop="position" content="2"></li>';

        } else {

            /**
             * その他のページ（無いと思うが一応）
             */
            echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><span itemprop="name">'. get_the_title() .'</span><meta itemprop="position" content="2"></li>';
        }

        echo '</ul></div>';  // 冒頭に合わせて閉じタグ

    }
}

/**
* ページネーション出力関数
* $paged : 現在のページ
* $pages : 全ページ数
* $range : 左右に何ページ表示するか
* $show_only : 1ページしかない時に表示するかどうか
*/
function pagination( $pages, $paged, $range = 2, $show_only = false ) {

    $pages = ( int ) $pages;    //float型で渡ってくるので明示的に int型 へ
    $paged = $paged ?: 1;       //get_query_var('paged')をそのまま投げても大丈夫なように

    //表示テキスト
    $text_first   = "« 最初へ";
    $text_before  = "‹ 前へ";
    $text_next    = "次へ ›";
    $text_last    = "最後へ »";

    if ( $show_only && $pages === 1 ) {
        // １ページのみで表示設定が true の時
        echo '<div class="pagination"><span class="current pager">1</span></div>';
        return;
    }

    if ( $pages === 1 ) return;    // １ページのみで表示設定もない場合

    if ( 1 !== $pages ) {
        //２ページ以上の時
        echo '<div class="pagination"><span class="page_num">Page ', $paged ,' of ', $pages ,'</span>';
        if ( $paged > $range + 1 ) {
            // 「最初へ」 の表示
            echo '<a href="', get_pagenum_link(1) ,'" class="first">', $text_first ,'</a>';
        }
        if ( $paged > 1 ) {
            // 「前へ」 の表示
            echo '<a href="', get_pagenum_link( $paged - 1 ) ,'" class="prev">', $text_before ,'</a>';
        }
        for ( $i = 1; $i <= $pages; $i++ ) {

            if ( $i <= $paged + $range && $i >= $paged - $range ) {
                // $paged +- $range 以内であればページ番号を出力
                if ( $paged === $i ) {
                    echo '<span class="current pager">', $i ,'</span>';
                } else {
                    echo '<a href="', get_pagenum_link( $i ) ,'" class="pager">', $i ,'</a>';
                }
            }

        }
        if ( $paged < $pages ) {
            // 「次へ」 の表示
            echo '<a href="', get_pagenum_link( $paged + 1 ) ,'" class="next">', $text_next ,'</a>';
        }
        if ( $paged + $range < $pages ) {
            // 「最後へ」 の表示
            echo '<a href="', get_pagenum_link( $pages ) ,'" class="last">', $text_last ,'</a>';
        }
        echo '</div>';
    }
}

// ヘッダーで読み込まれるスクリプトをフッターに移動
function my_init_action() {
  remove_action('wp_head','wp_print_head_scripts',9);
//   add_action('wp_footer','wp_print_head_scripts',5);
	
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles' );

  remove_action( 'wp_head', 'wp_generator' );
  remove_action( 'wp_head', 'wlwmanifest_link' );
  remove_action( 'wp_head', 'rsd_link' );
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
}
add_action('init','my_init_action');

function my_delete_local_jquery() {
	wp_deregister_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'my_delete_local_jquery' );

//投稿時にスラッグを自動的に日付にする
add_action('admin_head-post-new.php','set_slug_date');
function set_slug_date() {?>
<script language="javascript">
//<![CDATA[
jQuery(document).ready(function($){
    $('input#post_name').val("<?php echo date('Ymd');?>");
});
//]]>
</script>
<?php }

/*---------------------------
 カスタム投稿タイプのカテゴリーアーカイブを表示する
 ※todo:カスタム投稿タイプでＷＰコアのカテゴリーを使用する場合のみ有効化
---------------------------*/
function add_post_category_archive( $wp_query ) {
if ($wp_query->is_main_query() && $wp_query->is_category()) {
$wp_query->set( 'post_type', array('post','club-blog'));
}
}
add_action( 'pre_get_posts', 'add_post_category_archive' , 10 , 1);

/*---------------------------
 引数で受け取ったカテゴリーの記事件数を返却
---------------------------*/
function funcGetPostCountByCategorySlug($slug) {
	$cat = get_category_by_slug($slug);//特定のカテゴリースラッグを指定
	$chosen_id = $cat->term_id;//カテゴリーIDを取得
	$thisCat = get_category($chosen_id);//カテゴリーの詳細データを取得
	return $thisCat->count;//カテゴリーの記事件数を表示
}

/*---------------------------
 
---------------------------*/
function funcGetImageById($id,$size) {
	return wp_get_attachment_image_src($id,$size)[0];
}

function funcGetNoimageUrl($size) {
	return funcGetImageById(2857,$size);
}

function my_admin_style() {
  echo '<style>
  /*適用したいスタイルを記入〜/
  
  </style>'.PHP_EOL;
}
add_action('admin_print_styles', 'my_admin_style');

/* the_archive_title 余計な文字を削除 */
add_filter( 'get_the_archive_title', function ($title) {
    if (is_category()) {
        $title = single_cat_title('',false);
    } elseif (is_tag()) {
        $title = single_tag_title('',false);
	} elseif (is_tax()) {
	    $title = single_term_title('',false);
	} elseif (is_post_type_archive() ){
		$title = post_type_archive_title('',false);
	} elseif (is_date()) {
	    $title = get_the_time('Y年n月');
	} elseif (is_search()) {
	    $title = '検索結果：'.esc_html( get_search_query(false) );
	} elseif (is_404()) {
	    $title = '「404」ページが見つかりません';
	} else {

	}
    return $title;
});

//// 記事のPVを取得
//function getPostViews($postID)
//{
//    $count_key = 'post_views_count';
//    $count = get_post_meta($postID, $count_key, true);
//    if ($count=='') {
//        delete_post_meta($postID, $count_key);
//        add_post_meta($postID, $count_key, '0');
//        return "0 View";
//    }
//    return $count.' Views';
//}

//// 記事のPVをカウントする
//function setPostViews($postID)
//{
//    $count_key = 'post_views_count';
//    $count = get_post_meta($postID, $count_key, true);
//    if ($count=='') {
//        $count = 0;
//        delete_post_meta($postID, $count_key);
//        add_post_meta($postID, $count_key, '0');
//    } else {
//        $count++;
//        update_post_meta($postID, $count_key, $count);
//    }
//
//    // デバッグ start
//    //echo '<script>';
//    //echo 'console.log("postID: ' . $postID .'");';
//    //echo 'console.log("カウント: ' . $count .'");';
//    //echo '</script>';
//    // デバッグ end
//}
//

//---------------------------------------
// 投稿タイプごとにアーカイブ件数を設定
//---------------------------------------
//function change_posts_per_page($query) {
//    if ( is_admin() || ! $query->is_main_query() )
//        return;
// 
//    if ( $query->is_archive() ) { /* アーカイブページの時に表示件数を5件にセット */
//		if ( $query->is_tax('study_column_tag') || $query->is_tax('new_column_tag') || $query->is_post_type_archive('study-contents') || $query->is_post_type_archive('contents') ) {
//        	$query->set( 'posts_per_page', '10' );
//		}
//    }
//}
//add_action( 'pre_get_posts', 'change_posts_per_page' );

/*【管理画面】ACF Options Page の設定 */
if( function_exists('acf_add_options_page') ) {
  acf_add_options_page(array(
    'page_title' => 'シミュレーションシステム設定', // ページタイトル
    'menu_title' => 'シミュレーションシステム設定', // メニュータイトル
    'menu_slug' => 'simulation-settings', // メニュースラッグ
    'capability' => 'edit_posts',
    'redirect' => false
  ));
}