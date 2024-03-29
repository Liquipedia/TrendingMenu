<?php

namespace Liquipedia\Extension\TrendingMenu\Api;

use ApiBase;
use Liquipedia\Extension\TrendingMenu\Helper;
use Wikimedia\ParamValidator\ParamValidator;

class WikiList extends ApiBase {

	/**
	 *
	 */
	public function execute() {
		// Tell squids to cache
		$this->getMain()->setCacheMode( 'public' );
		// Set the squid & private cache time in seconds
		$this->getMain()->setCacheMaxAge( 300 );
		$data = $this->getRequest()->getText( 'data' );
		if ( $data === 'list' ) {
			$out = Helper::getWikiList();
		} elseif ( $data === 'hot' ) {
			$out = Helper::getWikiHotList();
		} else {
			$out = [ 'error' => $this->msg( 'wikilist-invalid-value' )->text() ];
		}
		$this->getResult()->addValue( null, $this->getModuleName(), $out );
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->msg( 'wikilist-shortdesc' )->text();
	}

	/**
	 * @return mixed
	 */
	public function getParamDescription() {
		return parent::getParamDescription();
	}

	/**
	 * @return array
	 */
	public function getExamplesMessages() {
		return [
			'action=wikilist&data=list' => 'wikilist-example',
		];
	}

	/**
	 * @return array
	 */
	public function getAllowedParams() {
		return [
			'data' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
				ApiBase::PARAM_HELP_MSG => 'wikilist-data',
			]
		];
	}

}
