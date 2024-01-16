<?php

namespace Aramis\Models;

use Aramis\Interfaces\IMember;

class Member implements IMember
{
    private int $id;
    private int $age;
    private array $subordinates;
    private array $history;
    private ?IMember $boss;
    private ?IMember $prison;

    public function __construct(int $id, int $age)
    {
        $this->id = $id;
        $this->age = $age;
        $this->subordinates = [];
        $this->history = [];
        $this->boss = null;
        $this->prison = null;
    }

    /**
     * Get member id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get member age
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * Add a new subordinate
     *
     * @param IMember $subordinate
     *
     * @return $this
     */
    public function addSubordinate(IMember $subordinate): IMember
    {
        $this->subordinates[$subordinate->getId()] = $subordinate;
        return $subordinate;
    }


    /**
     * Remove a subordinate
     *
     * @param IMember $subordinate
     *
     * @return IMember|null
     */
    public function removeSubordinate(IMember $subordinate): ?IMember
    {
        unset($this->subordinates[$subordinate->getId()]);
        
        return $this;
    }


    /**
     * Get the list of the subordinates
     * @return IMember[]
     */
    public function getSubordinates(): array
    {
        return $this->subordinates;
    }
    

    /**
     * Get his boss
     * @return IMember|null
     */
    public function getBoss(): ?IMember
    {
        return $this->boss;
    }


    /**
     * Set boss of the member
     *
     * @param IMember|null $boss
     *
     * @return $this
     */
    public function setBoss(?IMember $boss): IMember
    {
        $this->boss = $boss;
        if (isset($boss)) {
            $boss->addSubordinate($this);
        }

        return $this;
    }


    /**
     * Send member to prison with the successor boss
     *
     * @param IMember|null $boss
     *
     * @return void
     */
    public function sendToPrison(?IMember $boss): void
    {
        $this->prison = $boss;
    }


    /**
     * Release member from prison
     *
     * @param void
     *
     * @return void
     */
    public function releaseFromPrison(): void
    {
        unset($this->prison);
    }


    /**
     * Check if member in prison
     *
     * @param void
     *
     * @return bool
     */
    public function inPrison(): bool
    {
        return isset($this->prison);
    }


    /**
     * Get successor boss from member prison
     *
     * @param void
     *
     * @return IMember|null $boss
     */
    public function getBossFromPrison(): ?IMember
    {
        return $this->prison;
    }


    /**
     * Add member History
     *
     * @param IMember $member
     *
     * @return void
     */
    public function addHistory(): void
    {
        $this->history[] = clone($this);
    }


    /**
     * Get the member's history step Boss
     *
     * @param int $step
     *
     * @return IMember $boss
     */
    public function getHistoryBoss(int $step): ?IMember
    {
        return $this->history[$step]?->getBoss();
    }


    /**
     * Get the member's history step Subordinates
     *
     * @param int $step
     *
     * @return IMember[] $subordinates
     */
    public function getHistorySubordinates(int $step): array
    {
        return $this->history[$step]?->getSubordinates();
    }


    /**
     * Get the member's history step Boss not in prison
     *
     * @param int $step
     *
     * @return IMember $boss
     */
    public function getHistoryBossNotInPrison(int $step): ?IMember
    {
        $boss = $this->getHistoryBoss($step);
        while ($boss->inPrison()) {
            $boss = $boss->getBossFromPrison();
        }

        return $boss;
    }


    /**
     * Get the member's history step Subordinates not in prison
     *
     * @param int $step
     *
     * @return void
     */
    public function setHistorySubordinatesNotInPrison(int $step): void
    {
        foreach ($this->getSubordinates() as $subordinate) {
            $this->removeSubordinate($subordinate);
        }

        foreach ($this->getHistorySubordinates($step) as $subordinate) {
            if (!$subordinate->inPrison()) {
                $subordinate->getBoss()->removeSubordinate($subordinate);
                $subordinate->setBoss($this);
            }
            else {
                $this->removeSubordinate($subordinate);
            }
        }
    }
}