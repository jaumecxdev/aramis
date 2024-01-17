<?php

namespace Aramis\Models;

use Aramis\Interfaces\IMember;

class Member implements IMember
{
    private int $id;
    private int $age;
    private array $subordinates;
    private array $initialSubordinates;
    private ?IMember $boss;
    private ?IMember $initialBoss;
    private ?IMember $prison;

    public function __construct(int $id, int $age)
    {
        $this->id = $id;
        $this->age = $age;
        $this->subordinates = [];
        $this->initialSubordinates = [];
        $this->boss = null;
        $this->initialBoss = null;
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
        // Add subordinate to member
        $this->addInitialSubordinate($subordinate);
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
        // Add boss subordinate
        if (isset($boss)) {
            $boss->addSubordinate($this);
        }

        // Add boss to member
        $this->setInitialBoss($boss);
        $this->boss = $boss;

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
     * Get the member Boss not in prison
     *
     * @return IMember $boss
     */
    public function getBossNotInPrison(): ?IMember
    {
        $boss = $this->getInitialBoss();
        while (isset($boss) && $boss->inPrison()) {
            $boss = $boss->getBossFromPrison();
        }

        return $boss;
    }

    /**
     * Get the member Subordinates not in prison
     *
     * @return void
     */
    public function setSubordinatesNotInPrison(): void
    {
        // Remove the last subordinates when he went to jail
        foreach ($this->getSubordinates() as $subordinate) {
            $this->removeSubordinate($subordinate);
        }

        // Get initial subordinates not in prison
        foreach ($this->getInitialSubordinates() as $subordinate) {
            if (!$subordinate->inPrison()) {
                $subordinate->getBoss()->removeSubordinate($subordinate);
                $subordinate->setBoss($this);
                $subordinate->setSubordinatesNotInPrison();
            }
            else {
                $this->removeSubordinate($subordinate);
            }
        }
    }

    /**
     * Add initial subordinate
     *
     * @param IMember $subordinate
     *
     * @return void
     */
    private function addInitialSubordinate(IMember $subordinate): void
    {
        // Is this the first time this subordinate has a boss?
        // Set initial member subordinate
        if (is_null($subordinate->getBoss())) {
            $this->initialSubordinates[$subordinate->id] = $subordinate;
        }
    }

    /**
     * Set initial boss
     *
     * @param IMember|null $boss
     *
     * @return void
     */
    private function setInitialBoss(?IMember $boss): void
    {
        // Is this the first time this member has a boss?
        // Set initial boss
        if (!isset($this->initialBoss)) {
            $this->initialBoss = $boss;
        }
    }

    /**
     * Get the member's initial Boss
     *
     * @return IMember $boss
     */
    private function getInitialBoss(): ?IMember
    {
        return $this->initialBoss;
    }

    /**
     * Get the member's initial Subordinates
     *
     * @return IMember[] $subordinates
     */
    private function getInitialSubordinates(): array
    {
        return $this->initialSubordinates ?? [];
    }
}