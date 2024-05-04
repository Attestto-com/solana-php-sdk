<?php

namespace Attestto\SolanaPhpSdk\Util;

/**
 * @property bool $skipPreflight
 * @property Commitment $commitment
 * @property Commitment $preflightCommitment
 * @property int $maxRetries
 * @property int $minContextSlot
 */
class ConfirmOptions
{
    public bool $skipPreflight;
    public Commitment $commitment;
    public Commitment $preflightCommitment;
    public int $maxRetries;
    public int $minContextSlot;

    /**
     * @param bool $skipPreflight
     * @param Commitment|null $commitment
     * @param Commitment|null $preflightCommitment
     * @param int $maxRetries
     * @param int $minContextSlot
     */
    public function __construct(
        bool $skipPreflight = false,
        Commitment $commitment = new Commitment('confirmed'),
        Commitment $preflightCommitment = new Commitment('confirmed'),
        int $maxRetries = 0,
        int $minContextSlot = 0
    ) {
        $this->skipPreflight = $skipPreflight;
        $this->commitment = $commitment;
        $this->preflightCommitment = $preflightCommitment;
        $this->maxRetries = $maxRetries;
        $this->minContextSlot = $minContextSlot;
    }
}
