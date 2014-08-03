<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Runtime\Command;

use KoolKode\BPMN\Engine\AbstractBusinessCommand;
use KoolKode\BPMN\Engine\ProcessEngine;
use KoolKode\BPMN\Runtime\Command\SignalExecutionCommand;
use KoolKode\Util\Uuid;

/**
 * Delivers a message to an execution that has subscribed to the received message.
 * 
 * @author Martin Schröder
 */
class MessageEventReceivedCommand extends AbstractBusinessCommand
{
	protected $messageName;
	protected $executionId;
	protected $variables;
	
	public function __construct($messageName, UUID $executionId, array $variables = [])
	{
		$this->messageName = (string)$messageName;
		$this->executionId = $executionId;
		$this->variables = $variables;
	}
	
	public function getPriority()
	{
		return self::PRIORITY_DEFAULT - 100;
	}
	
	public function executeCommand(ProcessEngine $engine)
	{
		$sql = "	SELECT s.`id`, s.`execution_id`, s.`node`
					FROM `#__bpm_event_subscription` AS s
					WHERE s.`name` = :message
					AND s.`flags` = :flags
					AND s.`execution_id` = :eid
					ORDER BY s.`created_at`
					LIMIT 1
		";
		$stmt = $engine->prepareQuery($sql);
		$stmt->bindValue('message', $this->messageName);
		$stmt->bindValue('flags', ProcessEngine::SUB_FLAG_MESSAGE);
		$stmt->bindValue('eid', $this->executionId->toBinary());
		$stmt->execute();
		
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		
		if($row === false)
		{
			throw new \RuntimeException(sprintf('Execution %s has not subscribed to message %s', $this->executionId, $this->messageName));
		}
		
		$execution = $engine->findExecution($this->executionId);
		
		if($row['node'] !== NULL)
		{
			$execution->setNode($execution->getProcessDefinition()->findNode($row['node']));
			$execution->setTransition(NULL);
			
			$engine->syncExecutionState($execution);
		}
		
		$sql = "	DELETE FROM `#__bpm_event_subscription`
					WHERE `execution_id` = :eid
		";
		$stmt = $engine->prepareQuery($sql);
		$stmt->bindValue('eid', $execution->getId()->toBinary());
		$stmt->execute();
		
		$engine->pushCommand(new SignalExecutionCommand($execution, $this->messageName, $this->variables));
	}
}
