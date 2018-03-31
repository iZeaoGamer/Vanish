<?php

namespace ErkamKahriman\Vanish;

use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Vanish extends PluginBase implements Listener {

    const PREFIX = C::BLUE . "Vanish" . C::GRAY . " -> " . C::RESET;

    public $vanish = array();

    public function onEnable() {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new VanishTask($this), 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(C::GREEN . "Aktiviert.");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        $name = $sender->getName();
        if ($cmd->getName() == "supervanish") {
            if ($sender instanceof Player) {
                if ($sender->hasPermission("supervanish.spectate")) {
                    if ($this->vanish[$name] == false) {
                        $this->vanish[$name] = true;
                        $sender->sendMessage(self::PREFIX . C::GREEN . "§dYou are now super vanished!\n§5No one can see you!");
                    } else {
                        $this->vanish[$name] = false;
                        foreach ($this->getServer()->getOnlinePlayers() as $players){
                            $players->showPlayer($sender);
                        }
                        $sender->sendMessage(self::PREFIX . C::RED . "§6You are no longer vanished!\n§eEveryone can see you!");
                    }
                }
            } else {
                $sender->sendMessage(self::PREFIX . C::YELLOW . "§aYou need to be a Player.");
            }
        }
        return false;
    }

    public function onLogin(PlayerLoginEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        if (!isset($this->vanish[$name])) $this->vanish[$name] = false;
    }

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        if ($this->vanish[$name] == true) $this->vanish[$name] = false;
    }

    public function onDisable() {
        $this->getLogger()->info(C::RED . "Deaktiviert.");
    }
}
