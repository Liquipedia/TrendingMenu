<?php

namespace Liquipedia\TrendingMenu;

use SpecialPage;

class SpecialWikiList extends SpecialPage {

	/**
	 *
	 * @var Output
	 */
	private $output;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct( 'WikiList', 'edit-wikilist' );
	}

	/**
	 * @param string $param
	 */
	public function execute( $param ) {
		$user = $this->getUser();
		if ( !$this->userCanExecute( $user ) ) {
			$this->displayRestrictionError();
			return;
		}
		$this->output = $this->getOutput();
		$this->setHeaders();
		$this->displayWikiList();
		$this->output->addModules( 'ext.WikiList.drag' );
		$this->output->addModuleStyles( 'ext.WikiList.styles' );
	}

	private function displayWikiList() {
		$this->output->addWikiText( $this->msg( 'wikilist-update-wikis-page' )->text() );
		$list = Helper::getWikiList();
		$table = '<table class="wikitable">';
		$table .= '<tr>'
			. '<th>' . $this->msg( 'wikilist-heading-main-wikis' )->text() . '</th>'
			. '<th>' . $this->msg( 'wikilist-heading-alpha-wikis' )->text() . '</th>'
			. '<th>' . $this->msg( 'wikilist-heading-pre-alpha-wikis' )->text() . '</th>'
			. '</tr>';
		$table .= '<tr>';
		foreach ( $list as $type => $details ) {
			$table .= '<td style="vertical-align:top; align: center;">';
			$table .= '<ul id="' . $type . '" class = "wikilist-list">';
			if ( !isset( $details[ 'items' ] ) ) {
				continue;
			}
			foreach ( $details[ 'items' ] as $wikis ) {
				$table .= '<li '
					. 'class="wikilist-wiki-name" '
					. 'slug-name="' . $wikis[ 'slug' ] . '">'
					. $wikis[ 'title' ]
					. '</li>';
			}
			$table .= '</ul>';
			$table .= '</td>';
		}
		$table .= '</tr>';
		$table .= '</table>';
		$this->output->addHTML( $table );
		$this->output->addHTML(
			'<button id="wikilist-submit-button">'
			. $this->msg( 'wikilist-button-save' )->text()
			. '</button>'
		);
		$this->output->addHTML( '<p id="wikilist-button-click-notification"></p>' );
	}

}
