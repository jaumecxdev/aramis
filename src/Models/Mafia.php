<?php

namespace Aramis\Models;

use Aramis\Interfaces\IMafia;
use Aramis\Interfaces\IMember;

class Mafia implements IMafia
{
    private IMember $godfather;
    private array $members;

    /**
     * Initialize the object
     *
     * @param IMember $godfather
     */
    public function __construct(IMember $godfather)
    {
        $this->members = [];
        $this->addMember($this->setGodfather($godfather));
    }

    /**
     * Get the godfather of the organisation
     * @return IMember
     */
    public function getGodfather(): IMember
    {
        return $this->godfather;
    }

    /**
     * Set the godfather of the organisation
     * @return IMember
     */
    public function setGodfather(IMember $godfather): IMember
    {
        $this->godfather = $godfather;

        return $godfather;
    }

    /**
     * Add new member to the net
     *
     * @param IMember $member
     *
     * @return IMember|null
     */
    public function addMember(IMember $member): ?IMember
    {
        $this->members[$member->getId()] = $member;

        return $member;
    }

    /**
     * Remove member from the net
     *
     * @param IMember $member
     *
     * @return IMember|null
     */
    public function removeMember(IMember $member): ?IMember
    {
        unset($this->members[$member->getId()]);

        return $member;
    }

    /**
     * Get a member by id
     *
     * @param int $id
     *
     * @return IMember|null
     */
    public function getMember(int $id): ?IMember
    {
        return $this->members[$id] ?? null;
    }

    /**
     * Put a member in prison
     *
     * @param IMember $member
     *
     * @return bool
     */
    public function sendToPrison(IMember $member): bool
    {
        if (is_null($this->getMember($member->getId()))) {
            return false;
        }

        // Remove member from the net
        $this->removeMember($member);
        if (!is_null($member->getBoss())) {
            // Remove the subordinate member from the boss
            $member->getBoss()->removeSubordinate($member);
        }

        $this->restructuring($member);

        return true;
    }

    /**
     * Restructuring Mafia after a member in prison
     *
     * @param IMember $member
     *
     * @return void
     */
    private function restructuring(IMember $member): bool
    {
        $boss = null;
        $currentBoss = $member->getBoss();

        // The member is not the Godfather, Select boss in boss subordinates
        if (isset($currentBoss)) {
            $boss = $this->selectBossFromSubordinates($currentBoss->getSubordinates(), $member->getId());
        }

        // Select boss in member subordinates
        if (!isset($boss)) {
            $boss = $this->selectBossFromSubordinates($member->getSubordinates());
            // No subordinates || In prison || All Mafia members in Prison
            if (!isset($boss)) {
                return false;
            }

            // Set the member boss to new boss
            $boss->setBoss($currentBoss);
            // If the member was the Godfather, the new boss is now the Godfather
            if (!isset($currentBoss)) {
                $this->setGodfather($boss);
            }
        }

        // Send member to prison with the successor boss
        $member->sendToPrison($boss);
        // Assign the new boss to the member subordinates
        $this->setBossToSubordinates($boss, $member->getSubordinates());
        return true;
    }

    /**
     * Select the boss from the subordinates who are not in prison
     *
     * @param array $subordinates
     *
     * @return IMember $boss
     */
    private function selectBossFromSubordinates(?array $subordinates): ?IMember
    {
        $boss = null;
        foreach ($subordinates as $subordinate) {
            if (!$subordinate->inPrison() && (!isset($boss) || $boss->getAge() < $subordinate->getAge())) {

                $boss = $subordinate;
            }
        }

        return $boss;
    }

    /**
     * Set Boss to all subordinates
     *
     * @param array $subordinates
     * @param IMember $boss
     *
     * @return void
     */
    private function setBossToSubordinates(IMember $boss, ?array $subordinates): void
    {
        foreach ($subordinates as $subordinate) {
            if (!$subordinate->inPrison() && $subordinate->getId() != $boss->getId()) {

                $subordinate->setBoss($boss);
            }
        }
    }

    /**
     * Release a member from the prison
     *
     * @param IMember $member
     *
     * @return bool
     */
    public function releaseFromPrison(IMember $member): bool
    {
        if (!$member->inPrison()) {
            return false;
        }
       
        // Add member to the net
        $this->addMember($member);
        // Release member from prison
        $member->releaseFromPrison();
        // Set the member's boss from the beginning Not in prison
        $member->setBoss($member->getBossNotInPrison());
        // Set member subordinates from the beginning Not in prison
        $member->setSubordinatesNotInPrison();

        return true;
    }

    /**
     * Find bosses who have more than required number of subordinates
     *
     * @param int $minimumSubordinates
     *
     * @return IMember[]
     */
    public function findBigBosses(int $minimumSubordinates): array
    {
        $bigBosses = [];
        foreach ($this->members as $member) {
            if ($this->getCountSubordinates($member) > $minimumSubordinates) {
                $bigBosses[] = $member;
            }
        }

        return $bigBosses;
    }

    /**
     * Get count subordinates recursive
     *
     * @param IMember $member
     *
     * @return int
     */
    private function getCountSubordinates(IMember $member): int
    {
        $count = count($member->getSubordinates());
        foreach ($member->getSubordinates() as $subordinate) {
            $count += $this->getCountSubordinates($subordinate);
        }

        return $count;
    }

    /**
     * Compare two members between them and return the one with the highest level or null if they are equals
     *
     * @param IMember $memberA
     * @param IMember $memberB
     *
     * @return IMember|null
     */
    public function compareMembers(IMember $memberA, IMember $memberB): ?IMember
    {
        $countA = $this->getMemberLevel($memberA);
        $countB = $this->getMemberLevel($memberB);

        return $countA > $countB ? $memberB : ($countA < $countB ? $memberA : null);
    }

    /**
     * Get the member high level
     *
     * @param IMember $member
     *
     * @return int
     */
    private function getMemberLevel(IMember $member): int
    {
        $count = 0;
        $boss = $member->getBoss();
        while (!is_null($boss)) {
            $count++;
            $boss = $boss->getBoss();
        }

        return $count;
    }
}