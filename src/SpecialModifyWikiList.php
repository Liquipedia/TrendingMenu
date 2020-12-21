<?php

namespace Liquipedia\TrendingMenu;

use HTMLForm;
use SpecialPage;
use Status;

class SpecialModifyWikiList extends SpecialPage {

	/**
	 *
	 * @var array
	 */
	private $wikiTypes = [
		'Pre-Alpha Wiki' => 'preAlphaWiki',
		'Alpha Wiki' => 'alphaWiki',
		'Main Wiki' => 'mainWiki'
	];

	/**
	 *
	 */
	public function __construct() {
		parent::__construct( 'ModifyWikiList', 'see-modifywikilist' );
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
		$this->addWiki();
		$this->deleteWiki();
	}

	private function addWiki() {
		$heading = $this->msg( 'wikilist-heading-add-wiki' )->text();
		$this->output->addWikiText( $this->msg( 'wikilist-re-order-wikis-page' )->text() );
		$this->output->addWikiText( '==' . $heading . '==' );
		$formDescriptor = [
			'WikiName' => [
				'type' => 'text',
				'label-message' => 'wikilist-wikiname',
				'maxlength' => 100,
				'required' => true
			],
			'WikiSlug' => [
				'type' => 'text',
				'label-message' => 'wikilist-slug',
				'maxlength' => 100,
				'help-message' => 'wikilist-slug-help',
			],
			'WikiType' => [
				'type' => 'select',
				'label-message' => 'wikilist-type',
				'options' => $this->wikiTypes
			],
		];
		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->setSubmitTextMsg( 'wikilist-button-add-wiki' )
			->setFormIdentifier( 'add-wiki-form' )
			->setSubmitCallback( [ $this, 'addWikiCB' ] )
			->show();
	}

	/**
	 *
	 * @param array $formData
	 * @return Status
	 */
	public function addWikiCB( $formData ) {
		$name = $formData[ 'WikiName' ];
		if ( empty( trim( $name ) ) ) {
			$status = new Status;
			$status->error( 'wikilist-wiki-empty-error', $name );
			return $status;
		}
		if ( Helper::exists( $name ) ) {
			$status = new Status;
			$status->error( 'wikilist-wiki-already-exists', $name );
			return $status;
		}
		$type = $formData[ 'WikiType' ];
		if ( empty( trim( $formData[ 'WikiSlug' ] ) ) ) {
			$slug = str_replace( ' ', '', mb_strtolower( $name ) );
		} else {
			$slug = $formData[ 'WikiSlug' ];
		}
		$wiki = [
			'wiki' => $name,
			'slug' => $slug,
			'type' => $type
		];
		Helper::add( $wiki );
		$status = new Status;
		$status->warning( 'wikilist-wiki-added', $name );
		return $status;
	}

	private function deleteWiki() {
		$heading = $this->msg( 'wikilist-heading-delete-wiki' )->text();
		$this->output->addWikiText( '==' . $heading . '==' );
		$formDescriptor = [
			'WikiNameList' => [
				'type' => 'select',
				'label-message' => 'wikilist-wikiname',
				'options' => Helper::getWikiNamesForDropList()
			],
		];
		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->setSubmitTextMsg( 'wikilist-button-delete-wiki' )
			->setFormIdentifier( 'delete-wiki-form' )
			->setSubmitCallback( [ $this, 'deleteWikiCB' ] )
			->show();
	}

	/**
	 *
	 * @param array $formData
	 * @return Status
	 */
	public function deleteWikiCB( $formData ) {
		$wiki = $formData[ 'WikiNameList' ];
		Helper::delete( $wiki );
		$status = new Status;
		$status->warning( 'wikilist-wiki-deleted-success', $wiki );
		return $status;
	}

}
