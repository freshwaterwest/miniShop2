<?php

class msLinkRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'msLink';
	public $languageTopics = array('minishop2');
	public $permission = 'msproduct_save';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return true;
	}


	/** {@inheritDoc} */
	public function process() {
		$canRemove = $this->beforeRemove();
		if ($canRemove !== true) {
			return $this->failure($canRemove);
		}

		$link = $this->getProperty('link');
		$master = $this->getProperty('master');
		$slave = $this->getProperty('slave');

		if (!$link || !$master || !$slave) {
			return $this->failure('');
		}

		/* @var msLink $msLink */
		if (!$msLink = $this->modx->getObject('msLink', $link)) {
			return $this->failure($this->modx->lexicon('ms2_err_no_link'));
		}
		$type = $msLink->get('type');

		$q = $this->modx->newQuery('msProductLink');
		$q->command('DELETE');
		switch ($type) {
			case 'many_to_many':
			case 'one_to_one':
				$q->where(array('master' => $slave, 'OR:slave:=' => $slave));
			break;

			case 'one_to_many':
				$q->where(array('master' => $master));
			break;

			case 'many_to_one':
				$q->where(array('slave' => $slave));
			break;
		}
		$q->prepare();
		$q->stmt->execute();

		return $this->success('');
	}



}
return 'msLinkRemoveProcessor';
