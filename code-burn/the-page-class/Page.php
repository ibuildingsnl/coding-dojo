<?php

class Page
{
    public $uri = null;
    public $assigns = array();
    public $page = array();
    public $parent_node = 0;
    public $site_title = '';
    public $breadcrumbs = array();
    public $auto_include_dir = '';
    /* @public $smart Smarty */
    public $smarty = null;
    public $default_template = '';
    public $template = '';
    public $cms_login = null;
    public $user_login = null;
    public $available = true;
    public $is_user = false;
    public $is_admin = false;
    public $menu_items = array();
    public $caching = 1;
    public $cache_id = null;

    protected $_extra_request_parameters = array();

    /**
     * @param array $parameters
     */
    public function setExtraRequestParameters(array $parameters)
    {
        $this->_extra_request_parameters = array_values($parameters);
    }

    /**
     * @return array
     */
    public function getExtraRequestParameters()
    {
        return $this->_extra_request_parameters;
    }

    public function __construct($uri)
    {
        $this->connect_db();
        header('Content-Type: '.HEADER_CONTENT_TYPE);
        $this->smarty = new Smarty;

        if (isset($_GET['clear_cache']))
        {
            $this->smarty->clear_cache();
        }

        if (DEBUGGING)
        {
            $this->smarty->caching = false;
            if (trusted_ip())
            {
                $this->smarty->debugging = true;
            }
        }

        if (trusted_ip())
        {
            ini_set('display_errors', '1');
            error_reporting(
                E_ERROR | E_PARSE | E_WARNING
                | E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING);
        }
        else
        {
            $this->smarty->debugging = false;
            ini_set('display_errors', '0');
            error_reporting(0);
        }

        $this->smarty->template_dir = ROOT.'/site/templates';
        $this->smarty->compile_dir = ROOT.'/site/templates_c';
        $this->smarty->use_sub_dirs = true;
        $this->default_template = DEFAULT_TEMPLATE;

        if (!table_exists('content'))
        {
            require(ROOT.'/includes/install.php');
            install();
        }

        $this->add_title_part(SITE_TITLE);

        $this->cms_login = new LoginClass('admins', 'cms_login');
        $this->user_login = new LoginClass('users', 'user_login');

        if ($this->cms_login->isLoggedIn())
        {
            $this->is_admin = true;
        }

        if ($this->user_login->isLoggedIn())
        {
            $this->is_user = true;
        }

        $parsed_url = parse_url($uri);
        $relative = ROOT;
        $url = $parsed_url['path'];
        $this->assign('header_content_type', HEADER_CONTENT_TYPE);
        $this->determine_page($url);
        if ($this->smarty->is_cached('', 'page_'.$this->page['id']))
        {
            $this->smarty->display(
                $this->default_template,
                'page_'.$this->page['id']);
            exit;
        }

        $this->smarty->register_function(
            'translate',
            'smarty_function_translate'
        );
        $this->smarty->register_modifier(
            'translate',
            'smarty_modifier_translate'
        );
        $this->smarty->register_function(
            'url_for',
            'smarty_function_url_for'
        );

        $this->open_page();
    }

    function get_page_info($id)
    {
        $result = mysql_query(
            "SELECT c.id, c.uri, t.title, t.menu_name, " .
            "c.node, c.skip_to_first_subpage FROM content c ".
            "WHERE c.id='$id';"
        );
        if ($result && mysql_num_rows($result))
        {
            return mysql_fetch_assoc($result);
        }
        return false;
    }

    function determine_page($url)
    {
        $url_parts = explode('/', trim($url, '/'));

        $page_ids = array();
        $uri_prefix = '';
        $parent_id = 0;

        foreach($url_parts as $key => $part)
        {
            if ($key == 0 || empty($part))
            {
                unset($url_parts[$key]);

                continue;
            }

            $result = mysql_query("SELECT id, skip_to_first_subpage ".
                "FROM content c WHERE t.uri='".addslashes($part)."' ".
                "AND c.parent_id='$parent_id';");
            if (!$result)
            {
                throw new RuntimeException('MySQL error');
            }

            if (mysql_num_rows($result))
            {
                $page_id = mysql_fetch_assoc($result);
                $parent_id = $page_id['id'];
                $page_ids[] = $page_id;
                unset($url_parts[$key]);
            }
            else
            {
                break;
            }
        }

        // remaining URL parts are extra request parameters
        $this->setExtraRequestParameters($url_parts);

        while(empty($page_ids)
            || $page_ids[count($page_ids)-1]['skip_to_first_subpage'])
        {
            $page_id = $this->find_first_subpage(
                $page_ids[count($page_ids)-1]['id']);
            $result = mysql_query("SELECT id, skip_to_first_subpage ".
                "FROM content WHERE id='$page_id';");
            if ($result && mysql_num_rows($result))
                $page_ids[] = mysql_fetch_assoc($result);
            else
                break;
        }

        $page = array();
        foreach($page_ids as $id)
        {
            $page = $this->get_page_info($id['id']);
            if ($page)
            {
                $uri_prefix .= '/'.$page['uri'];
                $this->breadcrumbs[] = array('id' => $page['id'],
                    'uri' => $page['uri'], 'href' => $uri_prefix,
                    'menu_name' => $page['menu_name'],
                    'title' => $page['title']);
                if ($page['node']) $this->parent_node = $page['id'];

                $this->add_title_part($page['title']);
            }
            else
                break;
        }
        $this->page_url = $uri_prefix;
        $this->page = $page;
        $this->assign('breadcrumbs', $this->breadcrumbs);
        return true;
    }

    function redirect($page_id)
    {
        if ($this->page['id'] != $page_id && $page_id > 0)
        {
            session_write_close();
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$this->get_url($page_id));
            exit;
        }
    }

    function open_page()
    {
        $result = mysql_query("SELECT * FROM content c ".
            "WHERE id='{$this->page['id']}';");
        if ($result && mysql_num_rows($result))
        {
            $this->page = mysql_fetch_assoc($result);
            $this->page['contents'] = plain_text($this->page['contents']);

            if ($this->is_admin)
            {
                if (!$this->page['available_for_admins'])
                {
                    $this->page['contents'] =
                        TPL_NOT_AVAILABLE_FOR_ADMINS;
                    $this->available = false;
                }
                else if ($this->cms_login->user['id'] != 1
                    && !$this->page['available_for_guests']
                    && !$this->page['available_for_users']
                    && !$this->has_permission(
                        $this->cms_login->user['id'],
                        $this->page['id'])
                )
                {
                    $this->page['contents'] =
                        TPL_NOT_AVAILABLE_FOR_SPECIFIC_ADMIN;
                    $this->available = false;
                }
            }
            else if ($this->is_user)
            {
                if (!$this->page['available_for_users'])
                {
                    $this->page['contents'] =
                        TPL_NOT_AVAILABLE_FOR_USERS;
                    $this->available = false;
                }
            }
            else
            {
                if (!$this->page['available_for_guests'] && !$this->is_user)
                {
                    $this->page['contents'] =
                        TPL_NOT_AVAILABLE_FOR_GUESTS;
                    $this->available = false;
                }
            }

            if (!$this->page['show_contents'])
            {
                $this->page['contents'] = TPL_INVISIBLE;
                $this->available = false;
            }
        }
        else
        {
            $this->page['contents'] = TPL_NOT_FOUND;
        }
    }

    function has_permission($admin_id, $page_id)
    {
        $result = mysql_query("SELECT id FROM permissions WHERE ".
            "admin_id='$admin_id' AND page_id='$page_id';");
        if ($result && mysql_num_rows($result))
            return true;

        return false;
    }

    function find_first_subpage($parent_id = 0)
    {
        $result = mysql_query("SELECT id FROM content WHERE ".
            "parent_id='$parent_id' ORDER BY priority ASC, id ASC;");
        if ($result && mysql_num_rows($result))
            return mysql_result($result, 0, 0);
    }

    function show_page()
    {
        if ($this->available)
        {
            $this->cache_id = 'page_'.$this->page['id'];
            if ($this->page['include_file'] != ''
                && file_exists(ROOT.'/site/'.$this->page['include_file']))
            {
                include_once(ROOT.'/site/'.$this->page['include_file']);
            }
        }
        $this->menu = $this->load_menu();
        $this->assign('menu', $this->menu);
        $this->assign('page_id', $this->page['id']);
        $this->assign('site_title', $this->get_site_title());
        $this->assign('contents', $this->page['contents']);
        $this->assign('description', $this->page['description']);
        $this->assign('keywords', $this->page['keywords']);
        $this->assign('page_title', $this->page['title']);
        $this->assign('page_url', $this->page_url);

        $this->assign('subnavigation', $this->subnavigation());
        $this->assign('main_navigation', $this->main_navigation());
        $this->assign('is_admin', $this->is_admin);
        $this->assign('is_user', $this->is_user);
        $this->assign('parent_node', $this->parent_node);

        $this->assign('meta_title', $this->get_page_title(' - ', true));

        $this->assign('timers', sfTimerManager::getTimers());

        $this->smarty->caching = $this->caching;
        $this->smarty->display(
            ($this->template != '' ?
                $this->template
                : $this->default_template),
            $this->cache_id);
    }

    function add_title_part($title_part)
    {
        $this->title_parts[] = $title_part;
    }

    function get_title_parts()
    {
        return $this->title_parts;
    }

    function get_page_title($separator = ' - ', $reverse = false)
    {
        $title_parts = $this->get_title_parts();

        if ($reverse)
        {
            $title_parts = array_reverse($title_parts);
        }

        return implode($separator, $title_parts);
    }


    function menuitems($parent_id=0, $uri_prefix='')
    {
        $timer = sfTimerManager::getTimer('navigation');
        $timer->startTimer();

        $menu_items = array();
        $result = mysql_query("SELECT c.id, t.uri, t.menu_name ".
            "FROM content c "
    "LEFT JOIN content_translations t ON c.id = t.content_id ".
    "WHERE ".
    "c.parent_id='$parent_id' AND c.show_in_menu='1' AND (".
    ($this->is_admin ? "c.available_for_admins='1' OR ":'') .
    ($this->is_user ?
        "c.available_for_users='1'"
        : "c.available_for_guests='1'").
    ") ORDER BY c.priority ASC, c.id ASC;")
            or $this->trigger_error(mysql_error());
    if ($result && mysql_num_rows($result))
    {
        while ($item = mysql_fetch_assoc($result))
        {
            $item['href'] = $uri_prefix.$item['uri'];
            $menu_items[$item['id']] = $item;
        }
    }
    $timer->addTime();

    return $menu_items;
  }

    function load_menu()
    {
        $menu = array();
        $menu[0] = $this->menuitems(0, '/');
        foreach($this->breadcrumbs as $item)
        {
            $menu[$item['id']] = $this->menuitems(
                $item['id'],
                $item['href'].'/'
            );
        }
        return $menu;
    }

    function subnavigation()
    {
        if ($this->page['id'] != $this->parent_node
            && !empty($this->menu[$this->page['id']]))
            return $this->menu[$this->page['id']];
        else if ($this->page['parent_id'] != $this->parent_node
            && $this->page['parent_id'] != 0)
            return $this->menu[$this->page['parent_id']];
    }

    function main_navigation()
    {
        return $this->menu[$this->parent_node];
    }

    function get_uri_prefix($page_id)
    {
        $uri_prefix = '/';
        if ($page_id > 0)
        {
            foreach ($this->breadcrumbs as $item)
            {
                $uri_prefix .= $item['uri'].'/';
                if ($item['id'] == $page_id) break;
            }
        }
        return $uri_prefix;
    }

    public function get_url($page_id)
    {
        $timer = sfTimerManager::getTimer('get_url');
        $timer->startTimer();

        $url = '';
        while ($page_id > 0)
        {
            $result = mysql_query("SELECT c.id, c.parent_id, t.uri ".
                "FROM content c WHERE c.id='$page_id';");
            if ($result && mysql_num_rows($result))
            {
                $page = mysql_fetch_assoc($result);
                $url = '/'.$page['uri'].$url;
                $page_id = $page['parent_id'];
            }
            else {
                break;
            }
        }

        $timer->addTime();

        return $url;
    }

    function get_site_title()
    {
        $site_title = SITE_TITLE;
        foreach($this->breadcrumbs as $crumb)
        {
            if ($crumb['id'] == $this->page['id'])
                $crumb['title'] = $this->page['title'];
            if ($crumb['title'] != '')
                $site_title .= ' - '.$crumb['title'];
        }
        return $site_title;
    }

    public function connect_db()
    {
        $this->db_connection = @mysql_connect(
            MYSQL_HOST,
            MYSQL_USER,
            MYSQL_PASSWORD);
        if ($this->db_connection)
        {
            $this->db = @mysql_select_db(MYSQL_DB);
            if (!$this->db)
            {
                ?><p class="warning">Geen database.</p><?
                exit;
            }
        }
        else
        {
            ?><p class="warning">Geen verbinding.</p><?
            exit;
        }
    }

    /**
     * Add the given value under the given name to the list of variables that is available to Smarty
     *
     * @param $name
     * @param $value
     */
    function assign($name, $value)
    {
        $this->smarty->assign($name, $value);
    }

    function trigger_error($message, $error_type=E_USER_WARNING)
    {
        $this->smarty->trigger_error($message, $error_type);
    }
}
