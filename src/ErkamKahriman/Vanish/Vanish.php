<?php
namespace ErkamKahriman\Vanish;
use pocketmine\event\player\{PlayerLoginEvent, PlayerQuitEvent};
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
	self::$instance = $this;
	    $this->saveResource("config.yml");
        $this->getScheduler()->scheduleRepeatingTask(new VanishTask(), 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(C::GREEN . "Plugin enabled.");
    }
    public static function getInstance() : Vanish{
        return self::$instance;
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        $name = $sender->getName();
        if ($cmd->getName() == "supervanish") {
            if ($sender instanceof Player) {
                if ($sender->hasPermission("supervanish.spectate")) {
                    if ($this->vanish[$name] == false) {
                        $this->vanish[$name] = true;
                        $sender->sendMessage(self::PREFIX . C::GREEN . " §dYou are now vanished. §5No one can see you.\n§aKeep in mind - §bOnly use this command to catch hackers, or abusers, nothing else. \n§cAs this could cause a demotion if you do not obey the rules.");
                        $sender->setDisplayName("");
			$sender->setNameTag("");
			$sender->despawnFromAll();
                        $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), (99999999*20), (1), (false)));
                        $sender->getPlayer()->addTitle("§6§lVanish Mode", "§5§lis enabled!", 40, 100, 40);
			    $message = str_replace(['{player}'], [$sender->getName()], $this->getConfig()->get("fake-leave"));
                        $this->getServer()->broadcastMessage($message);
			$this->getServer()->removeOnlinePlayer($sender);
                    } else {
                        $this->vanish[$name] = false;
                        foreach ($this->getServer()->getOnlinePlayers() as $players){
                            $players->showPlayer($sender);
				$this->getServer()->addOnlinePlayer($sender);
                        }
                        $sender->sendMessage(self::PREFIX . C::RED . " §dYou are no longer vanished! §bEveryone can now see you!");
                        $sender->spawnToAll();
			$sender->setNameTag("§b[§5§lSTAFF§r§b] §d".$sender->getName());
			$sender->setDisplayName($sender->getName());
                        $sender->removeEffect(Effect::NIGHT_VISION);
                        $sender->getPlayer()->addTitle("§6§lVanish mode", "§c§lis Disabled", 40, 100, 40);
			    $message = str_replace(['{player}'], [$sender->getName()], $this->getConfig()->get("fake-join"));
                        $this->getServer()->broadcastMessage($message);
                    }
                }
            } else {
                $sender->sendMessage(self::PREFIX . C::YELLOW . " Please use this command in-game.");
        }
    } else {
	    $sender->sendMessage(self::PREFIX . C::RED . "§cThis command is for staff only!");
    }
        return true;
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
        $this->getLogger()->info(C::RED . "Plugin disabled.");
    }
}
