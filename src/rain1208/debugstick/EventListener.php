<?php

namespace rain1208\debugstick;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;


class EventListener implements Listener
{
    public function InteractEvent(PlayerInteractEvent $event)
    {
        if ($event->getAction() >= 2) return;
        $hand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($hand->getNamedTag()->hasTag("debug")) {
            $block = $event->getBlock();
            var_dump($block);
        }
    }

}