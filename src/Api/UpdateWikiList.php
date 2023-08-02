<?php

namespace Liquipedia\Extension\TrendingMenu\Api;

use ApiBase;
use Liquipedia\Extension\TrendingMenu\Helper;
use Wikimedia\ParamValidator\ParamValidator;

class UpdateWikiList extends ApiBase {

	/**
	 *
	 */
	public function execute() {
		$data = $this->getRequest()->getText( 'data' );
		Helper::update( $data );
		$out = [ 'result' => $this->msg( 'wikilist-update-success' )->text() ];
		$this->getResult()->addValue( null, $this->getModuleName(), $out );
	}

	/**
	 * @return bool
	 */
	public function mustBePosted() {
		return true;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->msg( 'updatewikilist-shortdesc' )->text();
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
	public function getAllowedParams() {
		return [
			'data' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
				ApiBase::PARAM_HELP_MSG => 'updatewikilist-data',
			]
		];
	}

}
