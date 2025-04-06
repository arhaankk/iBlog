<?php
/**
 * Page building class
 */

namespace IB;

class Page
{
    protected $app = null;
    protected $data = null;
    protected $pdo = null;
    protected $crumbs = [];

	protected function __construct(&$app)
	{
		$this->app = $app;
		$this->web = $app->config('web');
		$this->data = array(
			'root' => $this->getRoot(),
			'pages' => $this->getRoot().'pages',
			'actions' => $this->getRoot().'actions',
			'username' => 'Guest',
		);
		$this->session = $app->getClass('IB\Session');
		if ($this->session->isAuthenticated()) {
			$this->data['username'] = $this->session->getUser()['username'];
			$this->data['user_id'] = $this->session->getUser()['id'];
			$this->data['displayname'] = $this->session->getUser()['displayname'];
		}
		$this->addCrumb('iBlog', '{{PAGES}}');
	}

	/**
	 * Generate the root URL of the page
	 */
	public function getRoot() : string
	{
		return ($this->web['tls'] ? 'https://' : 'http://') . $this->web['hostname'] . $this->web['path'];
	}

	/**
	 * Format a template given a path
	 * Tokens can be passed via the parameters or Page->data
	 */
	protected function formatTemplate(string $name, array $values) : string
	{
		$template_path = \IB::getRootDir() . "/templates/$name.html";
		if (!file_exists($template_path)) {
			$this->app->error('An non-existant template was used.', 'Templating failed', "Path not found: $template_path");
		}
		return $this->formatTemplateString(file_get_contents($template_path), $values);
	}

	/**
	 * Format a template given a string
	 * Tokens can be passed via the parameters or Page->data
	 */
	protected function formatTemplateString(string $data, array $values) : string
	{
		try {
			$out = $data;
			/* Replace with explicit values */
			foreach ($values as $k => $v) {
				$t = '{{'.strtoupper($k).'}}';
				$out = str_replace($t, $v, $out);
			}
			/* Replace with page defaults */
			foreach ($this->data as $k => $v) {
				$t = '{{'.strtoupper($k).'}}';
				$out = str_replace($t, $v, $out);
			}
			return $out;
		} catch (\Error $e) {
			$this->app->error($e->getMessage(), 'Templating failed', $e->getTraceAsString());
		}
	}

	/**
	 * Set the page title
	 */
	public function setTitle(string $value) : void
	{
		$this->data['title'] = $value;
	}

	/**
	 * Set the page description
	 */
	public function setDescription(string $value) : void
	{
		$this->data['description'] = $value;
	}

	/**
	 * Print the header and navigation of the page
	 */
	public function preamble() : void
	{
		/* Generate metadata */
		$title = 'iBlog';
		$description = 'A page on iBlog.';
		if (!empty($this->data['title'])) {
			$title = $this->data['title'] . ' — iBlog';
		}
		if (!empty($this->data['description'])) {
			$title = $this->data['title'] . ' — iBlog';
		}
		echo $this->formatTemplate('header', array(
				'title' => $title,
				'description' => $description));

		/* Generate navbar */
		$nav = <<<'nav'
<nav>
	<span class="header-brand"><a href="{{PAGES}}/">iBlog</a></span>
	<ul class="header-nav">
nav;
		$nav .= '<li><a href="{{PAGES}}/">Home</a>';
		if ($this->session->isAuthenticated()) {

			$nav .= '<li><a href="{{PAGES}}/posts.php?user={{USER_ID}}">My Posts</a>';
			$nav .= '<li><a href="{{PAGES}}/blog-write.php">Write</a>';
		}
		$nav .= '<li><a href="{{PAGES}}/search/search.php">Search</a>';
		$nav .= <<<'nav'
	</ul>
	<div class="header-acct">
		<div class="dropdown"><a href="{{PAGES}}/profile.php">{{USERNAME}}</a>
			<ul class="dropdown">
nav;
		if ($this->session->isAdmin()) {
			$nav .= '<li><a href="{{PAGES}}/admin.php">Admin panel</a>';
		}
		if ($this->session->isAuthenticated()) {
			$nav .= '<li><a href="{{PAGES}}/logout.php">Log out</a>';
		} else {
			$nav .= '<li><a href="{{PAGES}}/signup.php">Sign up</a>';
			$nav .= '<li><a href="{{PAGES}}/signin.php">Sign in</a>';
		}
		$nav .= <<<'nav'
			</ul>
		</div>
	</div>
</nav>
nav;
		echo $this->formatTemplateString($nav, array());
		$this->breadcrumbs();
	}

	/**
	 * Print breadcrumbs given the necessary info
	 */
	protected function breadcrumbs() : void
	{
		echo '<ul class="breadcrumbs">';
		foreach ($this->crumbs as $crumb) {
			$path = htmlspecialchars($crumb['path']);
			$name = htmlspecialchars($crumb['name']);
			$data = "<li><a href=\"$path\">$name</a></li>";
			echo $this->formatTemplateString($data, array());
		}
		echo '</ul>';
	}

	/**
	 * Add a breadcrumb
	 */
	public function addCrumb(string $name, string $path) : void
	{
		$this->crumbs[] = ['name' => $name, 'path' => $path];
	}

	/**
	 * Print the footer / copyright notice
	 */
	public function epilogue() : void {
		echo <<<'footer'
<footer>
	<small>&copy; iBlog 2025</small>
</footer>
footer;
	}

	/**
	 * Get templating data.
	 */
	public function data(string $key) {
		return $this->data[$key];
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new Page($app);
	}
}
