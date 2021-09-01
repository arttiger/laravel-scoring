<?php

namespace Arttiger\Scoring\Interfaces;

/**
 * Interface Provider.
 */
interface Provider
{
    /**
     * @param array $params
     * @return array
     */
    public function getScoring(array $params = []): array;

    /**
     * @param array $params
     * @return array
     */
    public function sendFeedback(array $params = []): array;

    /**
     * @param array $params
     * @return array
     */
    public function getUbki(array $params = []): array;
}
