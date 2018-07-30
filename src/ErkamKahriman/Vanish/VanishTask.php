<?php

namespace ErkamKahriman\Vanish;

use pocketmine\scheduler\Task;

class VanishTask extends Task {

    private $plugin;

    public function __construct(Vanish $plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun(int $currentTick) {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            if($player->spawned(){
            if (Vanish::getInstance()->vanish[$player->getName()] == true){
                foreach (Server::getInstance()->getOnlinePlayers() as $players){
                    $players->hidePlayer($player);
                    }
                } else {
         foreach (Server::getInstance()->getOnlinePlayers() as $player){
             if($player->hasPermission("supervanish.see")){
                    if(Vanish::getInstance()->vanish[$player->getName()] == false){
                       foreach (Server::getInstance()->getOnlinePlayers() as $players){
                           $players->showPlayer($player);
                    }
                }
            }
        }
    }
        }
    }
}
