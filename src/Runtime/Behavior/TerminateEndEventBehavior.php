<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Runtime\Behavior;

use KoolKode\BPMN\Engine\AbstractActivity;
use KoolKode\BPMN\Engine\Event\ActivityCompletedEvent;
use KoolKode\BPMN\Engine\VirtualExecution;

/**
 * Terminates all executions within the same root scope including the root itself.
 * 
 * @author Martin Schröder
 */
class TerminateEndEventBehavior extends AbstractActivity
{
	/**
	 * {@inheritdoc}
	 */
	public function enter(VirtualExecution $execution)
	{
		$execution->getEngine()->debug('Reached terminate end event "{name}"', [
			'name' => $this->getStringValue($this->name, $execution->getExpressionContext())
		]);
		
		$engine = $execution->getEngine();
		$engine->notify(new ActivityCompletedEvent($execution->getNode()->getId(), $execution, $engine));
		
		$root = $execution->getScopeRoot();
		$root->setNode($execution->getNode());
		$root->setTransition($execution->getTransition());
		$root->terminate(false);
	}
}
