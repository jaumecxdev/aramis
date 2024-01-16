<?php

namespace Aramis\Interfaces;

interface IMember
{
    /**
     * Initialize the object
     *
     * @param int $id
     * @param int $age
     */
    public function __construct(int $id, int $age);

    /**
     * Get member id
     * @return int
     */
    public function getId(): int;

    /**
     * Get member age
     * @return int
     */
    public function getAge(): int;

    /**
     * Add a new subordinate
     *
     * @param IMember $subordinate
     *
     * @return $this
     */
    public function addSubordinate(IMember $subordinate): IMember;

    /**
     * Remove a subordinate
     *
     * @param IMember $subordinate
     *
     * @return IMember|null
     */
    public function removeSubordinate(IMember $subordinate): ?IMember;

    /**
     * Get the list of the subordinates
     * @return IMember[]
     */
    public function getSubordinates(): array;

    /**
     * Get his boss
     * @return IMember|null
     */
    public function getBoss(): ?IMember;

    /**
     * Set boss of the member
     *
     * @param IMember|null $boss
     *
     * @return $this
     */
    public function setBoss(?IMember $boss): IMember;

    /**
     * Send member to prison with the successor boss
     *
     * @param IMember|null $boss
     *
     * @return void
     */
    public function sendToPrison(?IMember $boss): void;

    /**
     * Release member from prison
     *
     * @param void
     *
     * @return void
     */
    public function releaseFromPrison(): void;

    /**
     * Check if member in prison
     *
     * @param void
     *
     * @return bool
     */
    public function inPrison(): bool;

    /**
     * Get successor boss from member prison
     *
     * @param void
     *
     * @return IMember|null $boss
     */
    public function getBossFromPrison(): ?IMember;

    /**
     * Add member History
     *
     * @param IMember $member
     *
     * @return void
     */
    public function addHistory(): void;

    /**
     * Get the member's history step Boss
     *
     * @param int $step
     *
     * @return IMember $boss
     */
    public function getHistoryBoss(int $step): ?IMember;

    /**
     * Get the member's history step Subordinates
     *
     * @param int $step
     *
     * @return IMember[] $subordinates
     */
    public function getHistorySubordinates(int $step): array;

    /**
     * Get the member's history step Boss not in prison
     *
     * @param int $step
     *
     * @return IMember $boss
     */
    public function getHistoryBossNotInPrison(int $step): ?IMember;

    /**
     * Get the member's history step Subordinates not in prison
     *
     * @param int $step
     *
     * @return void
     */
    public function setHistorySubordinatesNotInPrison(int $step): void;
}
