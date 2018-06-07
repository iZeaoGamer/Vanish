<?php

namespace ErkamKahriman\Vanish;

use pocketmine\scheduler\PluginTask;

class VanishTask extends PluginTask {

    private $plugin;

    public function __construct(Vanish $plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun(int $currentTick) {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
            if ($this->plugin->vanish[$player->getName()] == true){
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $players){
                    $players->hidePlayer($player);
                    }
                } else {
         foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
             if($player->hasPermission("supervanish.see")){
                    if($this->plugin->vanish[$player->getName()] == false){
                       foreach ($this->plugin->getServer()->getOnlinePlayers() as $players){
                           $players->showPlayer($player);
                    }
                }
            }
        }
    }
        }
    }
}
