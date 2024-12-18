<?php

namespace HeroQR\Contracts\Output;

interface OutputInterface {
    /**
     * Execute the operation with the given parameters.
     *
     * @param array $params The parameters required for the operation.
     * @return mixed The result of the operation, it can vary based on the operation.
     * @throws InvalidArgumentException If required parameters are missing or invalid.
     */
    public function execute(object $params) :mixed;
}
