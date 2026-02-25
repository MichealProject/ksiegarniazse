<?php

/**
 * Klasa do zarządzania treścią do wyświetlenia na stronie
 */
class Display
{
	private string $display = '';

	/**
	 * Dodaje treść do bufora wyświetlania
	 * 
	 * @param string $to_display Treść HTML do dodania
	 * @return void
	 */
	public function toDisplay(string $to_display): void
	{
		$this->display .= $to_display;
	}

	/**
	 * Zwraca całą zebraną treść
	 * 
	 * @return string
	 */
	public function display(): string
	{
		return $this->display;
	}
}

/**
 * Klasa do zarządzania i generowania szablonu HTML strony
 */
class Template
{
	// Podstawowe ustawienia
	private string $title = '';
	private string $favicon_address = '';

	// Arkusze stylów
	private string $stylesheet_address = '';
	private string $stylesheet_integrity = '';

	// Skrypty JavaScript
	private string $javascript_address = '';
	private string $jquery_address = '';
	private string $jquery_ui_css_address = '';
	private string $jquery_ui_js_address = '';

	// Komponenty strony
	private string $nav = '';
	private string $main = '';
	private string $footer = '';

	// Pozostałe zasoby
	private string $fontawesome_address = '';
	private string $cookie_consent_banner = '';

	/**
	 * Generuje finalny kod HTML strony na podstawie szablonu
	 * 
	 * @return string
	 */
	public function generateTemplate(): string
	{
		$template_content = file_get_contents(__DIR__ . '/template.html');

		$replacements = [
			'%TITLE%'                    => $this->title,
			'%FAVICON_ADDRESS%'          => $this->favicon_address,
			'%STYLESHEET_ADDRESS%'       => $this->stylesheet_address,
			'%STYLESHEET_INTEGRITY%'     => $this->stylesheet_integrity,
			'%JAVASCRIPT_ADDRESS%'       => $this->javascript_address,
			'%NAV%'                      => $this->nav,
			'%MAIN%'                     => $this->main,
			'%FOOTER%'                   => $this->footer,
			'%JQUERY_ADDRESS%'           => $this->jquery_address,
			'%JQUERY_UI_CSS_ADDRESS%'    => $this->jquery_ui_css_address,
			'%JQUERY_UI_JS_ADDRESS%'     => $this->jquery_ui_js_address,
			'%FONTAWESOME_ADDRESS%'      => $this->fontawesome_address,
			'%COOKIE_CONSENT_BANNER%'    => $this->cookie_consent_banner,
		];

		$rendered = str_replace(
			array_keys($replacements),
			array_values($replacements),
			$template_content
		);

		// Usuń puste znaczniki
		$rendered = preg_replace('/<script[^>]*src=""[^>]*><\/script>\s*/i', '', $rendered);
		$rendered = preg_replace('/<link[^>]*href=""[^>]*>\s*/i', '', $rendered);

		return $rendered;
	}

	// ====== Settery dla właściwości ======

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function setNav(string $nav): void
	{
		$this->nav = $nav;
	}

	public function setMain(string $main): void
	{
		$this->main = $main;
	}

	public function setFooter(string $footer): void
	{
		$this->footer = $footer;
	}

	/**
	 * Ustawia domyślne wartości dla szablonu
	 * 
	 * @return void
	 */
	public function setDefault(): void
	{
		$this->title = 'Księgarnia ZSE';
		$this->favicon_address = 'BRAK';
		$this->stylesheet_address = 'css/style.css?v=1.0';
		$this->javascript_address = 'js/global.js';
		$this->jquery_address = 'https://code.jquery.com/jquery-3.7.1.min.js';
		$this->jquery_ui_css_address = 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css';
		$this->jquery_ui_js_address = 'https://code.jquery.com/ui/1.13.2/jquery-ui.js';
		$this->fontawesome_address = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css';

		// Załaduj komponent nawigacji
		$nav_path = __DIR__ . '/nav.php';
		if (is_file($nav_path)) {
			ob_start();
			include $nav_path;
			$this->nav = ob_get_clean();
		} else {
			$this->nav = '';
		}

		// Załaduj stopkę
		$footer_path = __DIR__ . '/footer.html';
		$this->footer = is_file($footer_path) ? file_get_contents($footer_path) : '';

		// Załaduj banner zgody na cookies
		$cookie_path = __DIR__ . '/cookie-consent.php';
		if (is_file($cookie_path)) {
			ob_start();
			include $cookie_path;
			$this->cookie_consent_banner = ob_get_clean();
		} else {
			$this->cookie_consent_banner = '';
		}
	}
}
