<?php

declare(strict_types=1);

namespace MailExport;

class PeriodSplitter
{
    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime
     */
    private $to;

    /**
     * @var callable
     */
    private $formatter;

    /**
     * @param int $fromTimestamp
     * @param int $toTimestamp
     * @throws \Exception
     */
    public function __construct(int $fromTimestamp, int $toTimestamp)
    {
        if ($fromTimestamp < 0 || $toTimestamp < 0 || $fromTimestamp > $toTimestamp) {
            throw new \InvalidArgumentException("Invalid date period from $fromTimestamp to $toTimestamp");
        }
        $this->from = (new \DateTime())->setTimestamp($fromTimestamp);
        $this->to = (new \DateTime())->setTimestamp($toTimestamp);
    }

    /**
     * @param callable $formatter Get timestamp as argument and return formatted date.
     */
    public function setDateFormatter(callable $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @param int $fromTimestamp
     * @throws \Exception
     */
    public function setFromTimestamp(int $fromTimestamp) {
        $toTimestamp = $this->to->getTimestamp();
        if ($fromTimestamp < 0 || $fromTimestamp > $toTimestamp) {
            throw new \InvalidArgumentException("Invalid date period from $fromTimestamp to $toTimestamp");
        }
        $this->from = (new \DateTime())->setTimestamp($fromTimestamp);
    }

    /**
     * @param int $toTimestamp
     * @throws \Exception
     */
    public function setToTimestamp(int $toTimestamp) {
        $fromTimestamp = $this->from->getTimestamp();
        if ($toTimestamp < 0 || $fromTimestamp > $toTimestamp) {
            throw new \InvalidArgumentException("Invalid date period from $fromTimestamp to $toTimestamp");
        }
        $this->to = (new \DateTime())->setTimestamp($toTimestamp);
    }

    /**
     * @return array
     */
    public function splitByYear(): array
    {
        $result = [];
        $yearFrom = (int)$this->from->format('Y');
        $yearTo = (int)$this->to->format('Y');
        for ($year = $yearFrom; $year <= $yearTo; $year++) {
            $from = $year == $yearFrom
                ? $this->from->getTimestamp()
                : strtotime($year . '-01-01 00:00:00');
            $to = $year == $yearTo
                ? $this->to->getTimestamp()
                : strtotime($year . '-12-31 23:59:59');
            if ($this->formatter !== null) {
                $from = call_user_func($this->formatter, $from);
                $to = call_user_func($this->formatter, $to);
            }
            $result[] = [$from, $to];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function splitByMonth(): array
    {
        $result = [];
        $yearFrom = (int)$this->from->format('Y');
        $yearTo = (int)$this->to->format('Y');
        $monthFrom = (int)$this->from->format('n');
        $monthTo = (int)$this->to->format('n');
        for ($year = $yearFrom; $year <= $yearTo; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                if ($year == $yearFrom && $month < $monthFrom) {
                    continue;
                }
                $from = $year == $yearFrom && $month == $monthFrom
                    ? $this->from->getTimestamp()
                    : strtotime($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00');
                $lastMonthDay = $this->getNumberOfDays($month, $year);
                $to = $year == $yearTo && $month == $monthTo
                    ? $this->to->getTimestamp()
                    : strtotime($year . '-' . ($month < 10 ? '0' : '') . $month . '-' . $lastMonthDay . ' 23:59:59');
                if ($this->formatter !== null) {
                    $from = call_user_func($this->formatter, $from);
                    $to = call_user_func($this->formatter, $to);
                }
                $result[] = [$from, $to];
            }
        }
        return $result;
    }

    /**
     * @param int $month
     * @param int $year
     * @return int
     */
    private function getNumberOfDays(int $month, int $year): int {
        $days = [
            1 => 31,
            2 => $this->isLeapYear($year) ? 29 : 28,
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31,
        ];
        if (!isset($days[$month])) {
            throw new \InvalidArgumentException("Invalid month number $month. Expected integer from 1 to 12.");
        }
        return $days[$month];
    }

    /**
     * @param int $year
     * @return bool
     */
    private function isLeapYear(int $year): bool {
        return $year % 400 == 0 || $year % 4 == 0 && $year % 100 != 0;
    }
}

