<?php

namespace MCPluginTeam;

// Plugin

use pocketmine\plugin\PluginBase;

// Level

use pocketmine\level\Level;

// Command

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

// Event's

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;

// Item

use pocketmine\item\Item;

// Block

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;

// Utils

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

// Default's

use pocketmine\Server;
use pocketmine\Player;

class KitPVP extends PluginBase implements Listener {
	
  const PREFIX = "§7[§aKit§bP§cv§bP§7]§r ";
  
  public function onEnable(){

   //When Plugin started
   
   $this->getLogger()->info("§c============= §aMCPluginTeam §c=============");
   $this->getLogger()->info("§aPlugin: §aKitPVP");
   $this->getLogger()->info("§bAuthor: §bMCPluginTeam");
   $this->getLogger()->info("§c============= §aMCPluginTeam §c=============");
  
  $anticheat = $this->getServer()->getPluginManager()->getPlugin("AntiCheat-MCTeamPlugin");	
  
  
  if ($anticheat) {
  	
     $this->getLogger()->info("§7[§aCONNECT§7] §aSuccessly connected with AntiCheat by MCPluginTeam");
     
  } else {
  	
     $this->getLogger()->info("§7[§aCONNECT§7] §cPlease Download the AntiCheat by MCTeamPlugin to protect your Server!");
  
  }
  
   $this->getServer()->getPluginManager()->registerEvents($this, $this);
   
   if(is_dir("/mcpluginteam") !== true) {
		
	@mkdir("/mcpluginteam");
	
	}
	
	if(is_dir("/mcpluginteam/KitPVP") !== true) {
		
	@mkdir("/mcpluginteam/KitPVP");
	
	}
	
	if(is_dir("/mcpluginteam/KitPVP/maps") !== true) {
		
		@mkdir("/mcpluginteam/KitPVP/maps");
		
   }
   
     if(is_dir("/mcpluginteam/KitPVP/users") !== true) {
     	
     @mkdir("/mcpluginteam/KitPVP/users");
     
     }


   }
   
   public function onDeath(PlayerDeathEvent $event) {
		  $player = $event->getPlayer();
		
		    $cause = $player->getLastDamageCause();
            if ($cause instanceof EntityDamageByEntityEvent) {
            	
            	$killer = $cause->getDamager();
                if ($killer instanceof Player) {
                	
                   $playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $killer . ".yml");
                   $playerconfiguare->set("Kills", $playerconfiguare->get("Kills")+1);
                   $playerconfiguare->save();
                   
                   $deathplayer = $playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $player->getName() . ".yml");
                   $deathplayer->set("Deaths", $deathplayer->get("Deaths")+1);
                   $deathplayer->save();
                   
                   $event->setDeathMessage(self::PREFIX . "§a{$deathplayer} killed by {$killer}!");
                   
                     
                    } else {
                    	
                    $event->setDeathMessage(self::PREFIX . $player->getName() . " §cdied");
                    }
                    
                   }
                
                }
                
          public function onDrop(PlayerDropItemEvent $event) {
          	$player = $event->getPlayer();
           $playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $player->getName() . ".yml");
            if($playerconfiguare->get("InRound") === true) {
            	$event->setCancelled(true);
            } else {
            	$event->setCancelled(false);
            }
          }
          
          public function onRespawn(PlayerRespawnEvent $event) {
          	$player = $event->getPlayer();
           $playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $player->getName() . ".yml");
            if ($playerconfiguare->get("InRound") === true) {
              $inworld = $playerconfiguare->get("PlayedMap");
              $map = $this->getServer()->getLevelByName($inworld);
              $player->teleport($map->getSafeSpawn());
          
                 $player->setGamemode(0);
                 $player->setAllowFlight(false);
                 $player->getInventory()->clearAll();
                 $player->setHealth(20);
                 $player->setFood(20);
                 
                 $kitkopf = Item::get(310, 0, 1);
                 $kitchest = Item::get(311, 0, 1);
                 $kitleggit = Item::get(312, 0, 1);
                 $kitboots = Item::get(313, 0, 1);
                 
                 $player->getInventory()->addItem(Item::get(320, 0, 64));
                 $player->getInventory()->addItem(Item::get(276, 1, 1));
                 
                 $player->getArmorInventory()->setHelmet($kitkopf);
                 $player->getArmorInventory()->setChestplate($kitchest);
                 $player->getArmorInventory()->setLeggings($kitleggit);
                 $player->getArmorInventory()->setBoots($kitboots);
                 
          
          
          } else {
          	
              $defaultLevel = $this->getServer()->getDefaultLevel()->getSafeSpawn();
              $player->teleport($defaultLevel);
            
      
        }
        
      }
      
      public function onQuit(PlayerQuitEvent $event) {
      	$player = $event->getPlayer();
      
         $playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $player->getName() . ".yml");
         $playerconfiguare->set("InRound", false);
         $playerconfiguare->set("PlayedMap", null);
         $playerconfiguare->save();
      
         
      
      }
      
      public function onJoin(PlayerJoinEvent $event) {
         $player = $event->getPlayer();
      
         $player->getInventory()->clearAll();
         
         $defaultLevel = $this->getServer()->getDefaultLevel()->getSafeSpawn();
         $player->teleport($defaultLevel);
         
         $player->setHealth(20);
         $player->setFood(20);
      
      
      }
   
   public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
     if($command->getName() === "kitpvp") {
     	if(isset($args[0])) {
     	 if($args[0] === "make") {
     	   if(isset($args[1])) {
     	    if($sender->hasPermission("kitpvp.permission.admin")) {
             $playerinworld = $sender->getLevel()->getFolderName();
             
             $newmap = new Config("/mcpluginteam/KitPVP/maps/" . $args[1] . ".yml", Config::YAML);
             $newmap->set("WorldName", "$playerinworld");
             $newmap->set("Creator", $sender->getName());
             $newmap->save();
             
             $sender->sendMessage(self::PREFIX . "§aMap {$args[1]} successly created in world {$playerinworld}");
             } else {
              $sender->sendMessage(self::PREFIX . "§cYou have no Permissions for that!");
              }
             }
           }
           
           if($args[0] === "help") {
           	
              $sender->sendMessage("§c===================\n§a/kitpvp join map §7| §6Join a map\n§a/kitpvp make map §7| §6Create a Map\n§a/kitpvp fixmap map §7| §6AutoFix a Map\n§a/kitpvp leave §7| §6Leave a Map\n§c===================");
           	
           }
           
           if($args[0] === "leave") {
           	$playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $player->getName() . ".yml");
               $playerconfiguare->set("InRound", false);
               $playerconfiguare->set("PlayedMap", null);
               $playerconfiguare->save();
               
               $player->getInventory()->clearAll();
               $player->setHealth(20);
               $player->setFood(20);
               
               $defaultLevel = $this-getServer()->getDefaultLevel()->getSafeSpawn();
               $player->teleport($defaultLevel);
           }
           if($args[0] === "join") {
             if(isset($args[1])) {
               if(!is_file("/mcpluginteam/KitPVP/users/" . $sender->getName() . ".yml")) {
               	$playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $sender->getName() . ".yml", Config::YAML);
                   $playerconfiguare->set("Kills", null);
                   $playerconfiguare->set("Deaths", null);
                   $playerconfiguare->set("InRound", true);
                   $playerconfiguare->set("PlayedMap", "{$args[1]}");
                   $playerconfiguare->save();
               } else {
               	$playerconfiguare = new Config("/mcpluginteam/KitPVP/users/" . $sender->getName() . ".yml");
                   $playerconfiguare->set("InRound", true);
                   $playerconfiguare->set("PlayedMap", "{$args[1]}");
                   
                   $playerconfiguare->save();
                 }
             
           	$map = new Config("/mcpluginteam/KitPVP/maps/" . $args[1] . ".yml");
           	 if(!is_file("/mcpluginteam/KitPVP/maps/" . $args[1] . ".yml")) {
           	  $sender->sendMessage(self::PREFIX . "§cMap {$args[1]} not found!");
                 } else {
                 $mapworldname = $map->get("WorldName");
                 $this->getServer()->loadLevel($mapworldname);
                 $level = $this->getServer()->getLevelByName($mapworldname);
                 $sender->teleport($level->getSafeSpawn());
 
                 $sender->sendMessage("§c==================");
                 
                 $sender->sendMessage("§7|| §6Welcome in {$args[1]} §7||");
                 $sender->sendMessage("§7|| §6Creator : " . $map->get("Creator") . " §7||");
                 
                 $sender->sendMessage("§c==================");
                 
                 $sender->setGamemode(0);
                 $sender->setAllowFlight(false);
                 $sender->getInventory()->clearAll();
                 $sender->setHealth(20);
                 $sender->setFood(20);
                 
                 $kitkopf = Item::get(310, 0, 1);
                 $kitchest = Item::get(311, 0, 1);
                 $kitleggit = Item::get(312, 0, 1);
                 $kitboots = Item::get(313, 0, 1);
                 
                 $sender->getInventory()->addItem(Item::get(320, 0, 64));
                 $sender->getInventory()->addItem(Item::get(276, 1, 1));
                 
                 $sender->getArmorInventory()->setHelmet($kitkopf);
                 $sender->getArmorInventory()->setChestplate($kitchest);
                 $sender->getArmorInventory()->setLeggings($kitleggit);
                 $sender->getArmorInventory()->setBoots($kitboots);
                 
                }
               }
              }
                 
           
           if($args[0] === "fixmap") {
           	if(isset($args[1])) {
           	 $map = new Config("/mcpluginteam/KitPVP/maps/" . $args[1] . ".yml");
           	 if(!is_file("/mcpluginteam/KitPVP/maps/" . $args[1] . ".yml")) {
           	  $sender->sendMessage(self::PREFIX . "§cMap {$args[1]} not found!");
                 } else {
                 $mapworldname = $map->get("WorldName");
                 $this->getServer()->loadLevel($mapworldname);
                 $map->set("WorldName", "$mapworldname");
                 $map->save();
                 $sender->sendMessage(self::PREFIX . "§aMap {$args[1]} fixed!");
                 }
                }
               }
                 
           	 
              
           	
           
           
           }
          }
          return true;
         }
	   
	
	
	
}
