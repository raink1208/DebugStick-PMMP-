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

    private $TERRACOTTA = [
        BlockIds::PURPLE_GLAZED_TERRACOTTA,
        BlockIds::WHITE_GLAZED_TERRACOTTA,
        BlockIds::ORANGE_GLAZED_TERRACOTTA,
        BlockIds::MAGENTA_GLAZED_TERRACOTTA,
        BlockIds::LIGHT_BLUE_GLAZED_TERRACOTTA,
        BlockIds::YELLOW_GLAZED_TERRACOTTA,
        BlockIds::LIME_GLAZED_TERRACOTTA,
        BlockIds::PINK_GLAZED_TERRACOTTA,
        BlockIds::GRAY_GLAZED_TERRACOTTA,
        BlockIds::SILVER_GLAZED_TERRACOTTA,
        BlockIds::CYAN_GLAZED_TERRACOTTA,
        BlockIds::BLUE_GLAZED_TERRACOTTA,
        BlockIds::BROWN_GLAZED_TERRACOTTA,
        BlockIds::GREEN_GLAZED_TERRACOTTA,
        BlockIds::RED_GLAZED_TERRACOTTA,
        BlockIds::BLACK_GLAZED_TERRACOTTA
    ];

    private $directions =
        [
            "UP",
            "DOWN",
            "NORTH",
            "SOUTH",
            "WEST",
            "EAST"
        ];

    public function InteractEvent(PlayerInteractEvent $event)
    {
        if ($event->getAction() >= 2) return;
        $hand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($hand->getNamedTag()->hasTag("debug")) {
            $this->changeData($event->getPlayer(), $event->getBlock());
            $event->setCancelled();
        }
    }

    private function changeData(Player $player, Block $block)
    {
        $meta = $block->getDamage();
        switch ($block->getId()) {
            case BlockIds::WOOD:
            case BlockIds::WOOD2:
                $block->setDamage(($meta + 4 <= 15) ? $meta + 4 : abs(16 - ($meta + 4)));
                $player->sendActionBarMessage("向きを変更しました");
                break;

            case BlockIds::SIGN_POST:
            case BlockIds::STANDING_BANNER:
                $block->setDamage(($meta + 1 <= 15) ? $meta + 1 : 0);
                $player->sendActionBarMessage("向きを変更しました");
                break;

            case BlockIds::JACK_O_LANTERN:
                $block->setDamage(($meta + 1 <= 4) ? $meta + 1 : 1);
                $player->sendActionBarMessage("向きを変更しました");
                break;

            case BlockIds::RAIL:
            case BlockIds::POWERED_RAIL:
            case BlockIds::DETECTOR_RAIL:
            case BlockIds::ACTIVATOR_RAIL:
                $block->setDamage(($meta + 1 <= 9) ? $meta + 1 : 0);
                $player->sendActionBarMessage("向きを変更しました");
                break;

            case BlockIds::OAK_STAIRS:
            case BlockIds::STONE_STAIRS:
            case BlockIds::COBBLESTONE_STAIRS:
            case BlockIds::BRICK_STAIRS:
            case BlockIds::STONE_BRICK_STAIRS:
            case BlockIds::NETHER_BRICK_STAIRS:
            case BlockIds::SANDSTONE_STAIRS:
            case BlockIds::SPRUCE_STAIRS:
            case BlockIds::BIRCH_STAIRS:
            case BlockIds::JUNGLE_STAIRS:
            case BlockIds::QUARTZ_STAIRS:
            case BlockIds::ACACIA_STAIRS:
            case BlockIds::DARK_OAK_STAIRS:
            case BlockIds::RED_SANDSTONE_STAIRS:
            case BlockIds::PURPUR_STAIRS:
                $block->setDamage(($meta + 1 <= 7) ? $meta + 1 : 0);
                $player->sendActionBarMessage("向きを変更しました");
                break;

            case BlockIds::DISPENSER:
            case BlockIds::FURNACE:
            case BlockIds::BURNING_FURNACE:
            case BlockIds::CHEST:
            case BlockIds::ENDER_CHEST:
            case BlockIds::WALL_SIGN:
            case BlockIds::WALL_BANNER:
                $block->setDamage(($meta + 1 <= 5) ? $meta + 1 : 2);
                $d = $block->getDamage();
                $player->sendActionBarMessage("向きを" . $this->directions[$d] . "に変更しました");
                break;

            case in_array($block->getId(),$this->TERRACOTTA):
                $block->setDamage(($meta + 1 <= 5) ? $meta + 1 : 1);
                $player->sendActionBarMessage("向きを変更しました");
                break;

            default:
                $player->sendActionBarMessage($block->getName() . "はプロパティを持っていません");
        }
    }

    //連打防止
    public function onReceive(DataPacketReceiveEvent $event)
    {
        $packet = $event->getPacket();
        if ($packet instanceof InventoryTransactionPacket) {
            $player = $event->getPlayer();
            if ($player->getInventory()->getItemInHand()->getNamedTag()->hasTag("debug")) {
                $name = $player->getName();
                $time = ceil(microtime(true) * 1000);
                if (!isset($this->data[$name])) {
                    $this->data[$name] = $time;
                } else if ($time - $this->data[$name] >= 300) {
                    $this->data[$name] = $time;
                } else {
                    $event->setCancelled();
                }
            }
        }
    }
}