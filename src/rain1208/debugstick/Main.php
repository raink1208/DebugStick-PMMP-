<?php

namespace rain1208\debugstick;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($sender instanceof Player) {
            $item = Item::get(ITEM::STICK);
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1),1));
            $item->setCustomName("DebugStick");
            $nbt = new CompoundTag("",[]);
            $nbt->setString("debug","debug");
            $item->setNamedTag($nbt);
            $sender->getInventory()->addItem($item);
        }
        return true;
    }

}