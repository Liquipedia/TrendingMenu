<?php

namespace Liquipedia\Extension\TrendingMenu\Api;

use ApiBase;
use Liquipedia\Extension\TrendingMenu\Helper;

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
	public function getExamplesMessages() {
		return [
			'action=updatewikilist&data={json_format_data_goes_here}' => 'updatewikilist-example',
		];
	}

	/**
	 * @return array
	 */
	public function getAllowedParams() {
		return [
			'data' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_HELP_MSG => 'updatewikilist-data',
				ApiBase::PARAM_REQUIRED => true,
			]
		];
	}

}
