<?php

/*
 * This file is part of KoolKode BPMN.
 *
 * (c) Martin Schröder <m.schroeder2007@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KoolKode\BPMN\History;

use KoolKode\Util\UUID;

class HistoricProcessInstance implements \JsonSerializable
{
	protected $id;
	
	protected $processInstanceId;
	
	protected $processDefinitionId;
	
	protected $processDefinitionKey;
	
	protected $businessKey;
	
	protected $startActivityId;
	
	protected $endActivityId;
	
	protected $startedAt;
	
	protected $endedAt;
	
	protected $duration;
	
	public function __construct(UUID $id, UUID $processInstanceId, UUID $processDefinitionId, $processDefinitionKey, $startActivityId, \DateTimeInterface $startedAt)
	{
		$this->id = $id;
		$this->processInstanceId = $processInstanceId;
		$this->processDefinitionId = $processDefinitionId;
		$this->processDefinitionKey = (string)$processDefinitionKey;
		$this->startActivityId = (string)$startActivityId;
		$this->startedAt = clone $startedAt;
	}
	
	public function jsonSerialize()
	{
		return [
			'id' => $this->id,
			'processInstanceId' => $this->processInstanceId,
			'processDefinitionId' => $this->processDefinitionId,
			'processDefinitionKey' => $this->processDefinitionKey,
			'startActivityId' => $this->startActivityId,
			'endActivityId' => $this->endActivityId,
			'startedAt' => $this->startedAt->format(\DateTime::ISO8601),
			'endedAt' => ($this->endedAt === NULL) ? NULL : $this->endedAt->format(\DateTime::ISO8601),
			'duration' => $this->duration,
			'finished' => $this->isFinished()
		];
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getProcessInstanceId()
	{
		return $this->processInstanceId;
	}
	
	public function getProcessDefinitionId()
	{
		return $this->processDefinitionId;
	}
	
	public function getProcessDefinitionKey()
	{
		return $this->processDefinitionKey;
	}
	
	public function getBusinessKey()
	{
		return $this->businessKey;
	}
	
	public function setBusinessKey($businessKey = NULL)
	{
		$this->businessKey = ($businessKey === NULL) ? NULL : (string)$businessKey;
	}
	
	public function getStartActivityId()
	{
		return $this->startActivityId;
	}
	
	public function getEndActivityId()
	{
		return $this->endActivityId;
	}
	
	public function setEndActivityId($endActivityId = NULL)
	{
		$this->endActivityId = ($endActivityId === NULL) ? NULL : (string)$endActivityId;
	}
	
	public function getStartedAt()
	{
		return clone $this->startedAt;
	}
	
	public function getEndedAt()
	{
		return ($this->endedAt === NULL) ? NULL : clone $this->endedAt;
	}
	
	public function setEndedAt(\DateTimeInterface $endedAt = NULL)
	{
		$this->endedAt = ($endedAt === NULL) ? NULL : clone $endedAt;
	}
	
	public function getDuration()
	{
		return $this->duration;
	}
	
	public function setDuration($duration = NULL)
	{
		$this->duration = ($duration === NULL) ? NULL : (float)$duration;
	}
	
	public function isFinished()
	{
		return $this->endedAt !== NULL;
	}
}
