<?php

namespace CodingDojo\GildedRose;

class GildedRose
{
    private $items;

    const MAX_QUALITY = 50;

    const MIN_QUALITY = 0;

    function __construct($items)
    {
        $this->items = $items;
    }

    function update_quality()
    {
        foreach ($this->items as $item) {
            if ($this->isLegendary($item)) {
                continue;
            }

            $item->sell_in = $item->sell_in - 1;

            if ($this->isConcert($item)) {
                $item->quality = $this->appraiseConcertTicket($item);
            } elseif ($this->isBrie($item)) {
                $item->quality = $this->appraiseBrie($item);
            } elseif ($this->isConjured($item)){
                $item->quality = $this->appraiseConjured($item);
            } else {
                $item->quality = $this->appraiseOtherItem($item);
            }
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    private function isLegendary(Item $item)
    {
        return $item->name === 'Sulfuras, Hand of Ragnaros';
    }

    /**
     * @param $item
     * @return bool
     */
    private function isConcert($item)
    {
        return $item->name === 'Backstage passes to a TAFKAL80ETC concert';
    }

    /**
     * @param $item
     * @return bool
     */
    private function isBrie($item)
    {
        return $item->name === 'Aged Brie';
    }

    /**
     * @param $item
     * @return bool
     */
    private function isConjured($item)
    {
        return strpos($item->name, 'Conjured') === 0;
    }

    /**
     * @param $item
     * @return int|mixed
     */
    private function appraiseConcertTicket($item)
    {
        if ($item->sell_in < 0) {
            $newQuality = 0;
        } elseif ($item->sell_in < 5) {
            $newQuality = $item->quality + 3;
        } elseif ($item->sell_in < 10) {
            $newQuality = $item->quality + 2;
        } else {
            $newQuality = $item->quality + 1;
        }

        return min(self::MAX_QUALITY, $newQuality);
    }

    /**
     * @param $item
     * @return mixed
     */
    private function appraiseBrie($item)
    {
        if ($item->sell_in < 0) {
            $newQuality = $item->quality + 2;
        } else {
            $newQuality = $item->quality + 1;
        }

        return min(self::MAX_QUALITY, $newQuality);
    }

    /**
     * @param $item
     * @return mixed
     */
    private function appraiseOtherItem($item)
    {
        if ($item->sell_in < 0) {
            $newQuality = $item->quality - 2;
        } else {
            $newQuality = $item->quality - 1;
        }

        return max(self::MIN_QUALITY, $newQuality);
    }

    private function appraiseConjured($item)
    {
        if ($item->sell_in < 0) {
            $newQuality = $item->quality - 4;
        } else {
            $newQuality = $item->quality - 2;
        }

        return max(self::MIN_QUALITY, $newQuality);
    }
}
