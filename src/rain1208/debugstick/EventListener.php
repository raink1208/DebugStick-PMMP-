<?php

namespace rain1208\debugstick;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\Player;


class EventListener implements Listener
{
    private $data;

    public function InteractEvent(PlayerInteractEvent $event)
    {
        if ($event->getAction() >= 2) return;
        $hand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($hand->getNamedTag()->hasTag("debug")) {
            $this->changeData($event->getPlayer(),$event->getBlock());
        }
    }

    private function changeData(Player $player,Block $block)
    {
        $meta = $block->getDamage();
        switch ($block->getId()) {
            case BlockIds::WOOD:
                $block->setDamage(($meta+4 <= 15)? $meta+4:abs(16-($meta+4)));
                $player->sendActionBarMessage("向きを変更しました");
                break;
            case BlockIds::DISPENSER:
            case BlockIds::FURNACE:
            case BlockIds::BURNING_FURNACE:
            case BlockIds::CHEST:
            case BlockIds::ENDER_CHEST:
            case BlockIds::WALL_SIGN:
                $block->setDamage(($meta+1 <= 4)?$meta+1:0);
                $player->sendActionBarMessage("向きを変更しました");
                break;
            case BlockIds::SIGN_POST:
                $block->setDamage(($meta+1<=15)?$meta+1:0);
                $player->sendActionBarMessage("向きを変更しました");
            /*default:
                $player->sendActionBarMessage($block->getName()."はプロパティを持っていません");*/
        }
    }

    //連打防止
    public function onReceive(DataPacketReceiveEvent $event)
    {
        $player = $event->getPlayer();
        if ($player->getInventory()->getItemInHand()->getNamedTag()->hasTag("debug")) {
            $name = $player->getName();
            $packet = $event->getPacket();
            $time = ceil(microtime(true)*1000);
            if ($packet instanceof InventoryTransactionPacket) {
                if (!isset($this->data[$name])) {
                    $this->data[$name] = $time;
                } else if ($time - $this->data[$name] >= 500) {
                    $this->data[$name] = $time;
                } else {
                    $event->setCancelled();
                }
            }
        }
    }
}