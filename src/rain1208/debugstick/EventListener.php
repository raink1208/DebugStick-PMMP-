<?php

namespace rain1208\debugstick;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;


class EventListener implements Listener
{

    public function InteractEvent(PlayerInteractEvent $event)
    {
        if ($event->getAction() >= 2) return;
        $hand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($hand->getNamedTag()->hasTag("debug")) {
            $this->changeData($event->getPlayer(),$event->getBlock());
        }
    }

    private function changeData(Player $player,Block $block) {
        switch ($block->getId()) {
            case BlockIds::WOOD:
                $meta = $block->getDamage();
                $block->setDamage(($meta+4 <= 15)? $meta+4:abs(16-($meta+4)));
                $player->sendActionBarMessage("向きを変更しました");
            /*default:
                $player->sendActionBarMessage($block->getName()."はプロパティを持っていません");*/
        }
    }

}