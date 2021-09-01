<?php

namespace Arttiger\Scoring\Traits;

    use Arttiger\Scoring\Facades\Scoring;

    trait ScoringTrait
    {
        /**
         * Get scoring data.
         *
         * @param       $service
         * @param       $params
         *
         * @return mixed
         */
        public function scoring($service, $params = [])
        {
            if (method_exists($this, 'scoringAttributes')) {
                $this->scoringAttributes();
            }

            return Scoring::scoring($service, $this->getAttributes());
        }

        /**
         * Send status of credit.
         *
         * @param       $service
         * @param       $params
         *
         * @return mixed
         */
        public function status($service, $params = [])
        {
            if (method_exists($this, 'scoringAttributes')) {
                $this->scoringAttributes();
            }

            return Scoring::status($service, $this->getAttributes());
        }

        /**
         * Get UBKI reports.
         *
         * @param       $service
         * @param       $params
         *
         * @return mixed
         */
        public function ubkiReport($service, $params = [])
        {
            if (method_exists($this, 'scoringAttributes')) {
                $this->scoringAttributes();
            }

            return Scoring::ubkiReport($service, $this->getAttributes());
        }
    }
