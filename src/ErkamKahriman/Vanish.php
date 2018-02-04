<?php

namespace ErkamKahriman;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;
use pocketmine\entity\Entity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Vanish extends PluginBase implements Listener {

    const PREFIX = C::BLUE."Vanish".C::DARK_GRAY." >".C::WHITE." ";

    public $vanish = array();

    public function onEnable(){
        $this->getLogger()->info(C::GREEN."Aktiviert.");
        $this->saveResource("config.yml");
        @mkdir($this->getDataFolder());
        if (!file_exists($this->getDataFolder()."config.yml")){
            new Config($this->getDataFolder()."config.yml", Config::YAML, [
                "Creative_Vanish" => true
            ]);
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args){
        $name = $sender->getName();
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        if($cmd->getName() == "vanish") {
            if ($sender instanceof Player) {
                if ($sender->hasPermission("vanish.use")) {
                    if (!in_array($name, $this->vanish)) {
                        $this->vanish[] = $name;
                        $sender->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
                        $sender->setNameTagVisible(false);
                        if ($config->get("Creative_Vanish") == true) {
                            $sender->setGamemode(1);
                        }
                        $sender->sendMessage(self::PREFIX. C::GREEN . "You are now vanished.");
                    } elseif (in_array($name, $this->vanish)) {
                        unset($this->vanish[array_search($name, $this->vanish)]);
                        $sender->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, false);
                        $sender->setNameTagVisible(true);
                        if ($config->get("Creative_Vanish") == true) {
                            $sender->setGamemode(0);
                        }
                        $sender->sendMessage(self::PREFIX. C::RED . "You are no longer vanished!");
                    }
                }
            }
        }
    }

    public function onDisable(){
        $this->getLogger()->info(C::RED."Deaktiviert.");
    }
}