<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Task\Command;

use KoolKode\BPMN\Engine\AbstractBusinessCommand;
use KoolKode\BPMN\Engine\ProcessEngine;
use KoolKode\Util\UUID;

class UnclaimUserTaskCommand extends AbstractBusinessCommand
{
	protected $taskId;
	
	public function __construct(UUID $taskId)
	{
		$this->taskId = $taskId;
	}
	
	public function executeCommand(ProcessEngine $engine)
	{
		$task = $engine->getTaskService()
					   ->createTaskQuery()
					   ->taskId($this->taskId)
					   ->findOne();
		
		$sql = "	UPDATE `#__bpm_user_task`
					SET `claimed_at` = :time,
						`claimed_by` = :assignee
					WHERE `id` = :id
		";
		$stmt = $engine->prepareQuery($sql);
		$stmt->bindValue('time', NULL);
		$stmt->bindValue('assignee', NULL);
		$stmt->bindValue('id', $task->getId()->toBinary());
		$stmt->execute();
		
		$engine->debug('User task "{task}" unclaimed', [
			'task' => $task->getName()
		]);
	}
}
