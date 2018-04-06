<?php
namespace ErkamKahriman\Vanish;
use pocketmine\event\player\{PlayerLoginEvent, PlayerQuitEvent, PlayerJoinEvent};
use pocketmine\plugin\PluginBase;
use pocketmine\entity\{Effect, EffectInstance};
use pocketmine\{Player, Server};
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\command\{Command, CommandSender};
class Vanish extends PluginBase implements Listener {
    const PREFIX = C::BLUE . "§7[" . C::GRAY . "§aSuper§6Vanish§7]" . C::RESET;
    public $vanish = array();
    public function onEnable() {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new VanishTask($this), 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(C::GREEN . "Plugin enabled.");
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        $name = $sender->getName();
        if ($cmd->getName() == "supervanish") {
            if ($sender instanceof Player) {
                if ($sender->hasPermission("supervanish.spectate")) {
                    if ($this->vanish[$name] == false) {
                        $this->vanish[$name] = true;
                        $sender->sendMessage(self::PREFIX . C::GREEN . " §dYou are now vanished. §5No one can see you.");
                        $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), (99999999*20), (1), (false)));
                        $sender->getPlayer()->addTitle("§6§lVanish Mode", "§5§lis enabled!", 40, 100, 40);
                        $this->getServer()->broadcastMessage(C::GREEN . "§c$name §ehas left the game.");
                    } else {
                        $this->vanish[$name] = false;
                        foreach ($this->getServer()->getOnlinePlayers() as $players){
                            $players->showPlayer($sender);
                        }
                        $sender->sendMessage(self::PREFIX . C::RED . " §dYou are no longer vanished! §bEveryone can now see you!");
                        $sender->removeEffect(Effect::NIGHT_VISION);
                        $sender->getPlayer()->addTitle("§6§lVanish mode", "§c§lis Disabled", 40, 100, 40);
                        $this->getServer()->broadcastMessage(C::RED . "§a$name §ehas joined the game");
                    }
                }
            } else {
                $sender->sendMessage(self::PREFIX . C::YELLOW . " Please use this command in-game.");
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
        if ($this->vanish[$name] == true) $this->vanish[$name] = true;
    }
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        if ($this->vanish[$name] == true) $this->vanish[$name] = true;
        $player->sendMessage("§dYou're still in vanish mode! Just a little reminder. :P");
        }
    } else {
        if ($this->vanish[$name] == false) $this->vanish[$name] == false;
    }
    public function onDisable() {
        $this->getLogger()->info(C::RED . "Plugin disabled.");
    }
}
